#!/bin/bash

# Check if an argument was provided
if [ -z "$1" ]; then
  echo "Usage: $0 docker"
  echo "Usage: $0 podman"
  exit 1
fi

input="$1"

# Conditional logic based on string value
if [ "$input" == "docker" ]; then

  echo "Using Docker..."

  docker build -t rotexsoft/serverinfo .
  docker rm -f dockerized-serverinfo
  docker run --name dockerized-serverinfo  -dit -p 8080:80 --restart=always -v .:/var/www/html rotexsoft/serverinfo:latest
  docker exec dockerized-serverinfo /bin/chgrp -R www-data /var/www/html/logs/ /var/www/html/tmp/ /var/www/html/storage/
  docker exec dockerized-serverinfo /bin/chmod -R g+w /var/www/html/logs/ /var/www/html/tmp/ /var/www/html/storage/

  # open a bash session
  #docker exec -it dockerized-serverinfo /bin/bash

elif [ "$input" == "podman" ]; then

  echo "Using Podman..."

  # To run this script on s-edm-vpi057101 you need to be switched to the podman user
  # sudo -i -u podman
  # Then cd to /home/podman/promis-2.0
  # Then run this script

  # Look into https://docs.docker.com/engine/containers/start-containers-automatically/ to make sure this container always restarts
  podman build -t rotexsoft/serverinfo .
  podman rm -f dockerized-serverinfo
  # Remove --restart=always if you run into trouble
  podman run --name dockerized-serverinfo  -dit -p 8080:80 --restart=always -v .:/var/www/html:Z --cap-add CAP_NET_BIND_SERVICE rotexsoft/serverinfo:latest
  podman exec dockerized-serverinfo /bin/chgrp -R www-data /var/www/html/logs/ /var/www/html/tmp/ /var/www/html/storage/
  podman exec dockerized-serverinfo /bin/chmod -R g+w /var/www/html/logs/ /var/www/html/tmp/ /var/www/html/storage/
  # List all processes
  #podman ps --all
  # Stop Container
  #podman container stop dockerized-serverinfo
  # Examine container logs
  #podman logs dockerized-serverinfo
  # open a bash session
  #podman exec -it dockerized-serverinfo /bin/bash

else

  echo "Unknown command: $input"
fi
