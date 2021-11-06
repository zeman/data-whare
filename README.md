# DataWhare

[**Whare**](https://maoridictionary.co.nz/search?idiom=&phrase=&proverb=&loan=&histLoanWords=&keywords=whare) (Māori noun) house, building, residence, dwelling, shed, hut, habitation.

Data driven home automation. Collects data from various sensors and then triggers other actions in the house.

![Tesla charging from surplus solar](https://github.com/zeman/data-whare/blob/main/public/img/tesla-charging-from-solar.png?raw=true "Tesla charging from surplus solar")

## Solar

Monitor solar production from [Enphase Envoy](https://www4.enphase.com/en-in/products/envoy) and charge a [Tesla](https://www.tesla.com) with excess production rather than sending it to the grid.

Supported solar:
- Enphase Envoy

Supported Tesla apps:
- Teslafi

## Irrigation

Monitor [soil moisture](https://www.davisinstruments.com/products/soil-moisture-sensor-vantage-pro-and-vantage-pro2) via Davis Weatherlink and irrigate garden zone via [Rainmachine.](https://www.rainmachine.com)

## Getting up and running

Designed to run on a Raspberry Pi within your home local network. Uses Docker to make the setup simple.

### Docker on Raspberry Pi

Install [Ubuntu Server 20 LTS 64-bit](https://ubuntu.com/tutorials/how-to-install-ubuntu-on-your-raspberry-pi#1-overview
) on your Raspberry Pi.

Install Docker using normal [instructions for Ubuntu](https://docs.docker.com/engine/install/ubuntu/)

It's best to then install docker-compose via pip3 using the following commands.

```
sudo apt-get install libffi-dev libssl-dev
sudo apt install python3-dev
sudo apt-get install -y python3 python3-pip
sudo pip3 install docker-compose
```

git clone into a directory where you want to keep the app.

`git clone https://github.com/zeman/data-whare.git data-whare`

Open newly created directory.

`cd data-whare`

Run composer via docker.

`docker run --rm --volume $PWD:/app composer:latest composer install`

Build and start the app. This can take 10min to build the Docker containers.

`./vendor/bin/sail up -d`
`cp .env.example .env`
`./vendor/bin/sail artisan key:generate`
`./vendor/bin/sail artisan migrate`

Open up a new screen process to run scheduled jobs.

`screen`
`./vendor/bin/sail artisan schedule:work`
`control+a` then `d` to exit screen while leaving the process running
`screen -r` to resume the screen session

Visit the IP of you Raspberry Pi in your browser and follow the instructions.
