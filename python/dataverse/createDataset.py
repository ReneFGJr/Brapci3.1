#pip install pyDataverse
from pyDataverse.models import Dataset
from pyDataverse.utils import read_file

print("Hello World!")
ds = Dataset()
ds_filename = "../../.tmp/dataverse/dataset/dataset.json"
ds.from_json(read_file(ds_filename))
print("Result")
ds.get()