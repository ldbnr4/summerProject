#!/bin/bash

unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    cmp -s ${OPENSHIFT_REPO_DIR}/pyJamBaseBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyJamBaseBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp ${OPENSHIFT_REPO_DIR}/pyJamBaseBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyJamBaseBot.py
    fi
    cd ${OPENSHIFT_REPO_DIR}/ENV/bin
else
    cmp -s pyJamBaseBot.py ENV/bin/pyJamBaseBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp pyJamBaseBot.py ENV/bin/pyJamBaseBot.py
    fi
    cd ENV/bin
fi

source activate
python -c "import pyJamBaseBot; pyJamBaseBot.getEvents(\"$1\"); "