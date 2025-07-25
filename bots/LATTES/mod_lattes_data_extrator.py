def extract_element(element, tag, data_dict):
    """
    Extracts the attributes or text content of a specific tag from an XML element
    and stores it in a dictionary.

    :param element: The XML element to search within.
    :param tag: The tag to extract.
    :param data_dict: The dictionary to store the extracted data.
    """
    sub_element = element.find(tag)
    if sub_element is not None:
        if sub_element.attrib:
            data_dict[tag] = sub_element.attrib
        elif sub_element.text and sub_element.text.strip():
            data_dict[tag] = sub_element.text.strip()
        else:
            data_dict[tag] = None  # Tag vazia
    else:
        data_dict[tag] = None  # Tag não encontrada

    print("Elemento não encontrado:", data_dict)