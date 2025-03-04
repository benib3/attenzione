#!/bin/bash

# Set working directory to project root
cd /home/your-profile/dir/attenzione

# Set display for notify-send to work
export DISPLAY=:0
export XAUTHORITY=/home/user/.Xauthority
export DBUS_SESSION_BUS_ADDRESS="unix:path=/run/user/$(id -u)/bus"

# Process each URL from the config file
cat config/urls.example.txt | grep -v "^#" | while IFS="|" read -r URL TAG TAG_NAME || [[ -n $URL ]]; do
  # Skip empty lines
  [ -z "$URL" ] && continue
  
  # Trim whitespace
  URL=$(echo "$URL" | xargs)
  TAG=$(echo "$TAG" | xargs)
  TAG_NAME=$(echo "$TAG_NAME" | xargs)
  
  echo "Checking $URL for $TAG with name $TAG_NAME"
  
  # Run the command
  php bin/console app:run-notify "$URL" "$TAG" "$TAG_NAME" >> var/log/cron.log 2>&1
  
  # Wait a bit between requests to avoid hammering servers
  sleep 2
done