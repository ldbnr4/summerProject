#!/bin/bash
# use predefined variables to access passed arguments
#echo arguments to the shell

virtualenv ENV
cp pyDates.py ENV/bin
cp pyJamBaseBot.py ENV/bin
cp pyPicBot.py ENV/bin
cd ENV/bin
source activate
pip install lxml
pip install requests
pip install urllib2
pip install HTMLParser
pip install datetime
pip install python-dateutil
python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "