#!/bin/bash

echo '\nmid3 a: '
res=${PWD##*/}
echo $res

cd ENV/bin
source activate
echo '\nmid3 b: '
res=${PWD##*/}
echo $res

python -c "import pyPicsiBot; pyPicBot.getPic(\"$1\"); "