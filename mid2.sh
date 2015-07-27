#!/bin/bash

virtualenv ENV
cp pyDates.py ENV/bin
cp pyJamBaseBot.py ENV/bin
cp pyPicBot.py ENV/bin
echo '\nmid2 a: '
res=${PWD##*/}
echo $res
cd ENV/bin
echo '\nmid2 b: '
res=${PWD##*/}
echo $res
source activate
pip install lxml
pip install requests
pip install urllib2
pip install HTMLParser
pip install datetime
pip install python-dateutil