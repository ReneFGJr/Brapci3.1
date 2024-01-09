rem ng build --output-path ../../public/app --base-href './'.
ng build --output-path ../../public/app
echo "Copiando arquivo"
copy redirect_index.php ..\..\public\app\index.php
