# Data Whare

[**Whare**](https://maoridictionary.co.nz/search?idiom=&phrase=&proverb=&loan=&histLoanWords=&keywords=whare) (noun) house, building, residence, dwelling, shed, hut, habitation.

Data driven home automation. Collects data from various sensors and then triggers other actions in the house.

## Solar

Monitor solar production from [Enphase Envoy](https://www4.enphase.com/en-in/products/envoy) and charge [Tesla](https://www.tesla.com) with excess production rather than send to the grid.

## Irrigation

Monitor [soil moisture](https://www.davisinstruments.com/products/soil-moisture-sensor-vantage-pro-and-vantage-pro2) via Davis Weatherlink and irrigate garden zone via [Rainmachine.](https://www.rainmachine.com)

## Docker on Raspberry Pi

Install Ubuntu server
https://ubuntu.com/tutorials/how-to-install-ubuntu-on-your-raspberry-pi#1-overview

Install docker using normal instructions
https://docs.docker.com/engine/install/ubuntu/

Instal docker-compose via pip3

```
sudo apt-get install libffi-dev libssl-dev
sudo apt install python3-dev
sudo apt-get install -y python3 python3-pip
sudo pip3 install docker-compose
```

git clone into dir
cd into dir
run composer?
./vendor/bin/sail up -d


./vendor/bin/sail artisan make:model House -mc
./vendor/bin/sail artisan schedule:work
