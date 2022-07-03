echo off
echo "Language"
mkdir app\Language\pt-BR
copy ..\Brapci3.0\app\Language\pt-BR\social.* app\Language\pt-BR\*.* 

echo "Copiando Helper"
copy ..\Brapci3.0\app\Helpers\*.* app\Helpers\*.* 
copy ..\Brapci3.0\app\Models\Social*.* app\Models\*.*

echo "AI"
mkdir app\Models\AI
mkdir app\Models\AI\NLP
copy ..\Brapci3.0\app\Models\AI\NLP\*.php app\Models\AI\NLP\*.* 

echo "RDF"
mkdir app\Models\Rdf
copy ..\Brapci3.0\app\Models\RDF\RDF*.php app\Models\RDF\*.* 

echo "Metadata"
mkdir app\Models\Metadata
copy ..\Brapci3.0\app\Models\Metadata\*.php app\Models\Metadata\*.* 

echo "IO"
mkdir app\Models\Io
copy ..\Brapci3.0\app\Models\Io\*.php app\Models\Io\*.* 
