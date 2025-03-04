# Attenzione - Web Element Monitor

A Symfony command-line application that monitors web pages for specific HTML elements and sends notifications when they're found.

## Overview

Attenzione is a web scraping tool that checks websites for the presence of specific HTML elements. When a targeted element is found, it provides desktop notifications and logs the results. This is useful for:

- Monitoring product availability
- Tracking changes on websites
- Receiving alerts when specific content appears

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- Symfony CLI (optional, but recommended)

### Setup

1. Clone the repository:

```bash
git clone https://github.com/yourusername/attenzione.git
cd attenzione
```

2. Install dependencies:

```bash
composer install
```

3. Ensure the log directory exists and is writable:

```bash
mkdir -p var/log
chmod 755 var/log
```

## Usage

### Command Syntax

The basic command format is:

```bash
php bin/console app:run-notify <url> <tag> <tagName>
```

Where:

- `<url>`: The URL of the page to check
- `<tag>`: The HTML tag or CSS selector to search for
- `<tagName>`: The name attribute value or class name to match

### Examples

Check if there's a button with name "add-to-cart" on a page:

```bash
php bin/console app:run-notify https://www.example.com "button" "add-to-cart"
```

Check for a div with a specific name:

```bash
php bin/console app:run-notify https://store.example.org "div" "product-container"
```

## Setting Up Automated Monitoring

### Running as a Cron Job

To automatically run the command at regular intervals, you can set up a cron job.

1. Create a shell script wrapper:

```bash
#!/bin/bash
# /path/to/attenzione/bin/cron-run-notify.sh

# Set working directory
cd /path/to/attenzione

# Set display for desktop notifications
export DISPLAY=:0
export XAUTHORITY=/home/$USER/.Xauthority
export DBUS_SESSION_BUS_ADDRESS="unix:path=/run/user/$(id -u)/bus"

# Run the command
php bin/console app:run-notify "$1" "$2" "$3" >> var/log/cron.log 2>&1
```

2. Make the script executable:

```bash
chmod +x bin/cron-run-notify.sh
```

3. Add a cron job:

```bash
crontab -e
```

4. Add an entry to check every 15 minutes:

```
*/15 * * * * /path/to/attenzione/bin/cron-run-notify.sh "https://www.example.com" "button" "add-to-cart"
```

### Checking Multiple URLs

To monitor multiple pages, create a configuration file and a script to process it:

1. Create the URLs file:

```
# config/urls-to-check.txt
# Format: URL|TAG|TAG_NAME
https://www.example.com|button|add-to-cart # add-to-cart represents here a attribute name
https://store.example.org|div|product # product represents here a className
```

2. Create a multi-URL processor script:

```bash
#!/bin/bash
# /path/to/attenzione/bin/cron-run.sh

cd /path/to/attenzione

export DISPLAY=:0
export XAUTHORITY=/home/$USER/.Xauthority
export DBUS_SESSION_BUS_ADDRESS="unix:path=/run/user/$(id -u)/bus"

# Process each URL
cat config/urls-to-check.txt | grep -v "^#" | while IFS="|" read -r URL TAG TAG_NAME || [[ -n $URL ]]; do
  [ -z "$URL" ] && continue

  URL=$(echo "$URL" | xargs)
  TAG=$(echo "$TAG" | xargs)
  TAG_NAME=$(echo "$TAG_NAME" | xargs)

  php bin/console app:run-notify "$URL" "$TAG" "$TAG_NAME" >> var/log/cron.log 2>&1

  sleep 2
done
```

3. Set up a cron job for the multi-URL script:

```
*/30 * * * * /path/to/attenzione/bin/cron-run-notify-multi.sh
```

## Notifications

Attenzione supports cross-platform desktop notifications:

1. Display a desktop notification
2. Write a message to the console
3. Log the event to tag-notifications.log

## Advanced Usage

### Using CSS Selectors

You can use more complex CSS selectors for the `<tag>` parameter:

```bash
php bin/console app:run-notify https://example.com "button.buy-now[data-product-id]" "add-to-cart"
```

## Tag Finding Strategy

Attenzione employs a multi-step strategy to find elements:

1. First tries to find elements with the specified tag name and checks their "name" attributes
2. If nothing found, tries to find elements by class name
3. Returns true if the specified tagName is found in any of these attributes

## Troubleshooting

### Desktop Notifications Not Working

If desktop notifications aren't appearing when run via cron:

1. Make sure you've set the DISPLAY, XAUTHORITY and DBUS_SESSION_BUS_ADDRESS variables
2. For Linux: Verify that notify-send is installed: `sudo apt install libnotify-bin`
3. For macOS: Ensure the script has permission to send notifications
4. For Windows: Install the BurntToast module: `Install-Module -Name BurntToast`
5. Test manually: `notify-send "Test" "This is a test notification"` (Linux)

### Command Failing Silently

Check the log files:

- Application logs: tag-notifications.log
- Cron execution logs: cron.log

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Built with Symfony Console Component
- Uses Symfony's HTTP Client and DomCrawler for web scraping
- Uses JoliNotif for cross-platform desktop notifications

---

_Note: For server environments without a GUI, alternative notification methods like email or webhooks are recommended._
