ng build --output-path res
cd ..
cd ..
del public\app\*
copy angular\BRP\res\*.* public\app\*
