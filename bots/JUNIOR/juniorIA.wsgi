import sys
import logging

logging.basicConfig(stream=sys.stderr)
sys.path.insert(0, " /data/Brapci3.1/bots/JUNIOR/")

from app import app as application