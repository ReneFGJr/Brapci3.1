cd \
mkdir ollama
cd ollama

pip install pygit2==1.15.1
git clone https://github.com/lllyasviel/Fooocus.git

cd Fooocus
python.exe entry_with_update.py --share --always-high-vram