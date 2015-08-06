#!/bin/bash

unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    cmp -s ${OPENSHIFT_REPO_DIR}/pyPicBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyPicBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp ${OPENSHIFT_REPO_DIR}/pyPicBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyPicBot.py
    fi
    cd ${OPENSHIFT_REPO_DIR}/ENV/bin
else
    cmp -s pyPicBot.py ENV/bin/pyPicBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp pyPicBot.py ENV/bin/pyPicBot.py
    fi
    cd ENV/bin
fi

source activate
python -c "import pyPicBot; pyPicBot.getPic(\"$1\"); "