"""Executar: python tests/regression/Task2002Test.py (sem acessar serviços reais)."""
import importlib.util
import io
from contextlib import redirect_stdout
from pathlib import Path
import sys
import unittest
from unittest.mock import MagicMock

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
        cursor.fetchall.side_effect = [[{"ID": 1, "present": 1}, {"ID": 2, "present": 0},
                                       {"ID": 3, "present": 0}], []]
        cursor.rowcount = 1
        session = MagicMock()
        session.get.return_value.json.side_effect = [{"status": "404"}, self.metadata(3)]
        output = io.StringIO()
        with redirect_stdout(output):
            result = task.update_dataset(connection, session, "https://example.test/{ID}")
        self.assertEqual((result["checked"], result["inserted"], result["existing"], result["failed"]), (3, 1, 1, 1))
        self.assertFalse(result["success"])
        self.assertEqual(session.get.call_count, 2)
        connection.rollback.assert_called_once()
        inserts = [call for call in cursor.execute.call_args_list if call.args[0].startswith("INSERT")]
        self.assertEqual(len(inserts), 1)
        self.assertIn("NOT EXISTS", inserts[0].args[0])
        self.assertIn("ID 3: inserido", output.getvalue())

    def test_shortcut_routes_to_2002(self):
        sys.path.insert(0, str(ROOT / "bots/CURADOR"))
        from chat.router import localizar
        self.assertEqual(localizar("elastic update_dataset"), 2002)
        self.assertEqual(localizar("elastic export"), 2000)


if __name__ == "__main__":
    unittest.main()
