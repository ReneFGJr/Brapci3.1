ECHO ".."
cd ..
ECHO ".."
cd ..
ECHO "Deletenado"
del public\app\*.js
del public\app\*.txt
del public\app\*.css
echo "Copiando"
copy angular\BRP\res\*.* public\app\*
