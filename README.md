# DataWhare

[**Whare**](https://maoridictionary.co.nz/search?idiom=&phrase=&proverb=&loan=&histLoanWords=&keywords=whare) (Māori noun) house, building, residence, dwelling, shed, hut, habitation.

Data driven home automation. Collects data from various sensors and then triggers other actions in the house.

![Tesla charging from surplus solar](https://github.com/zeman/data-whare/blob/main/public/img/tesla-charging-from-solar.png?raw=true "Tesla charging from surplus solar")

## Solar

Monitor solar production from [Enphase Envoy](https://www4.enphase.com/en-in/products/envoy) and charge [Tesla](https://www.tesla.com) with excess production rather than send to the grid.

## Irrigation

Monitor [soil moisture](https://www.davisinstruments.com/products/soil-moisture-sensor-vantage-pro-and-vantage-pro2) via Davis Weatherlink and irrigate garden zone via [Rainmachine.](https://www.rainmachine.com)

## Get up and running

Designed to run on a Raspberry Pi within your home local network. Uses Docker to make the setup simple.

### Docker on Raspberry Pi

Install [Ubuntu server](https://ubuntu.com/tutorials/how-to-install-ubuntu-on-your-raspberry-pi#1-overview
) on your Raspberry Pi.

Install Docker using normal [instructions for Ubuntu](https://docs.docker.com/engine/install/ubuntu/)

It's best to then install docker-compose via pip3 using the following command.

```
sudo apt-get install libffi-dev libssl-dev
sudo apt install python3-dev
sudo apt-get install -y python3 python3-pip
sudo pip3 install docker-compose
```

git clone into dir

cd into dir

todo: run composer via docker

./vendor/bin/sail up -d

visit IP of Pi in your browser
