"""Executar: python tests/regression/Task2002Test.py (sem acessar serviços reais)."""
import importlib.util
import io
from contextlib import redirect_stdout
from pathlib import Path
import sys
import unittest
from unittest.mock import MagicMock
from rich.console import Console

ROOT = Path(__file__).resolve().parents[2]
spec = importlib.util.spec_from_file_location("task_2002", ROOT / "bots/CURADOR/tasks/task_2002.py")
task = importlib.util.module_from_spec(spec)
spec.loader.exec_module(task)


class Task2002Test(unittest.TestCase):
    def metadata(self, identifier=2):
        return {"ID": identifier, "Class": "Book", "title": "Livro", "year": "????",
                "creator_author": [{"name": "Autor"}], "subject": {"pt": [{"name": "Tema"}]},
                "description": "Resumo", "DOI": "10.1234/test"}

    def test_mapping_and_invalid_metadata(self):
        values = task.dataset_values(self.metadata(), 2)
        self.assertEqual(values["COLLECTION"], "BK")
        self.assertEqual(values["AUTHORS"], "Autor")
        self.assertEqual(values["KEYWORDS"], "Tema")
        self.assertEqual(values["YEAR"], 0)
        self.assertEqual(values["new"], 1)
        with self.assertRaises(ValueError):
            task.dataset_values(self.metadata(3), 2)
        with self.assertRaises(ValueError):
            task.dataset_values({"status": "404"}, 2)

    def test_skips_existing_and_continues_after_error(self):
        connection = MagicMock()
        cursor = connection.cursor.return_value.__enter__.return_value
        cursor.fetchall.side_effect = [[{"ID": 2, "Class": "Book"},
                                       {"ID": 3, "Class": "Book"}], []]
        cursor.rowcount = 1
        session = MagicMock()
        session.get.return_value.json.side_effect = [{"status": "404"}, self.metadata(3)]
        show = MagicMock()
        result = task._update_dataset(connection, session, "https://example.test/{ID}", show)
        self.assertEqual((result["checked"], result["inserted"], result["existing"], result["failed"]), (2, 1, 0, 1))
        self.assertFalse(result["success"])
        self.assertEqual(session.get.call_count, 2)
        connection.rollback.assert_called_once()
        inserts = [call for call in cursor.execute.call_args_list if call.args[0].startswith("INSERT")]
        self.assertEqual(len(inserts), 1)
        self.assertIn("NOT EXISTS", inserts[0].args[0])
        selects = [call for call in cursor.execute.call_args_list if call.args[0].startswith('SELECT')]
        self.assertIn('LEFT JOIN brapci_elastic.dataset', selects[0].args[0])
        self.assertIn('d.ID IS NULL', selects[0].args[0])
        self.assertIn('r.cc_status < 9', selects[0].args[0])
        self.assertIn('JOIN brapci_rdf.rdf_class', selects[0].args[0])
        self.assertTrue(any(call.args[2:] == ('Livro', 'Inserido', 'green') for call in show.call_args_list))

    def test_silent_has_no_output(self):
        connection = MagicMock()
        connection.cursor.return_value.__enter__.return_value.fetchall.return_value = []
        output = io.StringIO()
        with redirect_stdout(output):
            result = task.update_dataset(connection, MagicMock(), 'https://example.test/{ID}', silent=True)
        self.assertEqual(output.getvalue(), '')
        self.assertEqual(result['checked'], 0)

    def test_error_details_below_panel(self):
        output = io.StringIO()
        result = dict(checked=1, inserted=0, existing=0, failed=1,
                      last_error={'ID': 42, 'error': 'Falha ao conectar: detalhes completos do problema'})
        Console(file=output, width=60).print(task._status_panel(result, status='Concluído'))
        rendered = output.getvalue()
        self.assertIn('Último problema', rendered)
        self.assertIn('ID 42: Falha ao conectar', rendered)
        self.assertIn('detalhes completos', rendered)
        self.assertGreater(rendered.index('Último problema'), rendered.index('Erros'))

    def test_mismatched_id_preserves_metadata_before_error(self):
        connection = MagicMock()
        cursor = connection.cursor.return_value.__enter__.return_value
        cursor.fetchall.side_effect = [[{'ID': 2503, 'Class': 'Book'}], []]
        session = MagicMock()
        session.get.return_value.json.return_value = self.metadata(999)
        result = task.update_dataset(connection, session, 'https://example.test/{ID}', silent=True)
        self.assertIn('solicitado 2503, retornado 999', result['last_error']['error'])
        output = io.StringIO()
        Console(file=output, width=100).print(task._status_panel(result))
        rendered = output.getvalue()
        for value in ['Livro', 'Autor', 'Tema', '999']:
            self.assertIn(value, rendered)
        self.assertLess(rendered.index('Palavras-chave'), rendered.index('Último problema'))
        self.assertEqual(result['inserted'], 0)

    def test_shortcut_routes_to_2002(self):
        sys.path.insert(0, str(ROOT / "bots/CURADOR"))
        from chat.router import localizar
        self.assertEqual(localizar("elastic update_dataset"), 2002)
        self.assertEqual(localizar("elastic export"), 2000)


if __name__ == "__main__":
    unittest.main()
