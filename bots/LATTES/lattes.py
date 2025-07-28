import mod_producao_artistica_cultural

# Exemplo de uso:
xml_path = 'sources/0024977948247395.xml'  # Caminho do arquivo XML
producoes = mod_producao_artistica_cultural.extrair_producao_artistica(xml_path)

print("Produções artísticas e culturais ...")
# Impressão simples dos resultados
for i, p in enumerate(producoes, 1):
    print(f"\n--- Produção {i} ---")
    for k, v in p.items():
        print(f"{k}: {v}")
