# DataWhare

[**Whare**](https://maoridictionary.co.nz/search?idiom=&phrase=&proverb=&loan=&histLoanWords=&keywords=whare) (Māori noun) house, building, residence, dwelling, shed, hut, habitation.

Data driven home automation. Collects data from various sensors and then triggers other actions in the house.

![Tesla charging from surplus solar](https://github.com/zeman/data-whare/blob/main/public/img/tesla-charging-from-solar.png?raw=true "Tesla charging from surplus solar")

## Sun

Monitor solar production and charge a Tesla with excess production rather than sending it to the grid. The charging rate (amps) of the Tesla is adjusted once per minute to use surplus power.

Supported solar inverters:
- Enphase Envoy
- Fronius

Supported Tesla apps:
- Teslafi

## Water

Coming next: Monitor [soil moisture](https://www.davisinstruments.com/products/soil-moisture-sensor-vantage-pro-and-vantage-pro2) via Davis Weatherlink and irrigate garden zones via [Rainmachine.](https://www.rainmachine.com)

## Getting up and running

**Note: This is alpha software currently under development. Looking for people familiar with Raspberry Pi and Docker to help test and provide feedback.**

Designed to run on a Raspberry Pi within your home local network. Should also be able to run anywhere you have Docker installed if you just want to try the app out.

Don't expose the Raspberry Pi to the internet as there is currently no protection for the Teslafi API token. 

### Docker on Raspberry Pi

Install [Ubuntu Server 22 LTS 64-bit](https://ubuntu.com/tutorials/how-to-install-ubuntu-on-your-raspberry-pi#1-overview
) on your Raspberry Pi.

Give your Pi a static IP, login and change the password. `apt-get update`, `apt-get upgrade` and `reboot` Ubuntu.

Install Docker using normal [instructions for Ubuntu](https://docs.docker.com/engine/install/ubuntu/)

Also follow the Docker post install steps to add your user to the docker group.

```
sudo usermod -aG docker $USER
newgrp docker
```

Git clone into a directory where you want to keep the app.

`git clone https://github.com/zeman/data-whare.git data-whare`

Open newly created directory.

`cd data-whare`

Run Composer via Docker to install needed libraries.

`docker run --rm --volume $PWD:/app composer:latest composer install`

Copy the .env file and then edit to adjust timezone to your location.

```
cp .env.example .env
nano .env
```

Build and start the app. This can take 10min to build the Docker containers.

```
./vendor/bin/sail up -d
```



Create a unique key for Laravel and create the database.

```
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

Open up a new screen process to run the scheduled jobs every minute.

```
screen
./vendor/bin/sail artisan schedule:work
```

You can then exit the screen process and leave it running on your Pi.

`control+a` then `d` 

Visit the IP of your Raspberry Pi in your browser and follow the instructions.

### Updating

To update the app, git pull from the app directory and then rebuild the database. This will also reset all your data.

```
git pull
./vendor/bin/sail artisan migrate:refresh
```

If you need to rebuild the docker images from scratch you can use the build and no cache commands.

```
./vender/bin/sail build --no-cache
```
