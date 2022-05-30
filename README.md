# About

We have a running Adobe Commerce instance that need to communicate with an external ERP for order fulfillment reasons. With every new order, some of its details have to be asynchronously transmitted to the ERP, using the Message Queue features provided by Adobe Commerce. Order transmission must be reliable.

## Demo Screenshots
- On every new order, the Message Queue should receive a new message containing the order ID, the email address of the user who placed the order, and the amount of items in the cart, and the same information should be logged using the default logger

![Order Success Page](/readme-media/order-success.jpeg "Order Success Page")

![Order add in queue](/readme-media/order-add-in-queue-for-erp-sync.jpeg "Order add in queue")

- Orders information in the Message Queue should be periodically processed and transmitted to the ERP

![Order transmit to ERP](/readme-media/order-transmit-to-erp.jpeg "Order transmit to ERP")

- Orders that are successfully transmitted to the ERP must have their status changed from “new” to “processing”

![Order status updated](/readme-media/order-status-also-updated.jpeg "Order status updated")

- Every transmission attempt must be logged to a database table. The following information must be recorded: order ID, timestamp, return code from the ERP

![Custom Log table updated](/readme-media/on-transmit-log-table-update.jpeg "Custom Log table updated")

- I used [Mock API](https://mockapi.io/) for ERP syncing.

![Mock API Data](/readme-media/Erp-Mock-API.jpeg "Mock API Data")

![Mock API Data](/readme-media/mock-api-synced-data.jpeg "Mock API Data")

![Mock API Schema](/readme-media/mock-api-schema.jpeg "Mock API Schema")

- The database table containing the transmission logs should never be empty. On table creation, a single line should be created, with order ID 0, current timestamp at creation time, and return code 999

![Custom Log table default properties](/readme-media/transmission-table-properties.jpeg "Custom Log table default properties")

### Note: Transmission log table generated by db_schema.xml.

- An html page reachable at /erp_sync/items/status should show the last 10 transmission attempts. This should not be part of the admin panel!

![Trasmission log via URL](/readme-media/get-transmission-via-url.jpeg "Trasmission log via URL")

- There should be a CLI command that show a list of the last 10 successful transmission attempts, or the last 10 failed transmission attempts, based on an argument passed to it

![ERP Transmission CLI Command](/readme-media/cli-command-registered.jpeg "ERP Transmission CLI Command")

![ERP Transmission CLI Success Case](/readme-media/get-transmission-via-cli-success-case.jpeg "ERP Transmission CLI Success Case")

![ERP Transmission CLI Error Case](/readme-media/get-transmission-via-cli-error-case.jpeg "ERP Transmission CLI Error Case")

# Code Documentation

ERP Order fullfillment Module core business logic persists in `magento2/app/code/Custom` vendor folder.

## Code Business Logic

### Asynchronous Orders Processing
- `Plugin/OrderManagement.php` is our after plugin for the method `OrderManagementInterface::place`, as it fires after the order has been persisted to the database and call the Queue Publisher to add order in a queue to be consumed later.

- `Model/Queue/Consumer.php` is our queue handler, which containes all the business logic to: 
  - Sync the order to ERP.
  - Store Response to `erp_transmission_log` table
  - Update order status from `new/pending` to `processing`


# Run Application via Docker 

## Requirements
- [Docker](https://docs.docker.com/install)
- [Docker Compose](https://docs.docker.com/compose/install)

## Setup
- Clone Repository

### Start all docker containers

`docker-compose up -d`

### Configurations via CLI
- `docker exec -it magento2-order-async_apache_1 bash`

- `composer install`

- `php bin/magento setup:install \
  --db-host magento2-order-async_db_1 --db-name magento2 --db-user magento2 --db-password magento2  --admin-user admin --timezone 'Europe/Rome' --currency EUR --use-rewrites 1 --cleanup-database \
  --backend-frontname admin --admin-firstname AdminFirstName --admin-lastname AdminLastName --admin-email 'admin@email.com' --admin-password 'click123' --base-url 'http://magento2.docker/' --language en_US \
  --session-save=redis --session-save-redis-host=sessions --session-save-redis-port=6379 --session-save-redis-db=0 --session-save-redis-password='' \
  --cache-backend=redis --cache-backend-redis-server=cache --cache-backend-redis-port=6379 --cache-backend-redis-db=0 \
  --page-cache=redis --page-cache-redis-server=cache --page-cache-redis-port=6379 --page-cache-redis-db=1 \
  --search-engine=elasticsearch7 --elasticsearch-host=elasticsearch`

   - Set IP with domain `magento2.docker` in `/etc/hosts` file via `sudo /etc/hosts`

   - Set Permissions 
    - `find . -type d -exec chmod 755 {} \;`
    - `find . -type f -exec chmod 644 {} \;`
    - `find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} \;`
    - `find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} \;`
    - `chown -R :www-data .` 
    - `chmod u+x bin/magento`
   
    - `php bin/magento s:up && bin/magento s:d:c && bin/magento s:s:d -f en_US && bin/magento c:f`

   - Run this command on the root of project directory not in docker container `docker inspect magento2-order-async_apache_1` get IPAddress from Networks.

   ![Apache Local IP](/readme-media/apache-network-ip.jpeg "Apache Local IP")

## Run Application
   
- Now try to run(http://magento2.docker/) for storefront and (http://magento2.docker/admin) for backoffice
   - Admin Username: `admin`
   - Admin Password: `click123`

   - To access the DB run `docker inspect magento2-order-async_db_1` and get IPAddress from Network, and connect with any MySql Client with the provided details
   
   ![DB Local IP](/readme-media/db-local-ip.jpeg "DB Local IP")
   - Host: `IPADDRESS`
   - Database: `magento2`
   - Username: `magento2`
   - Password: `magento2`
   - Port: `3306`

   - Place and order and view the transmission logs in `erp_transmission_log` table

    ![Log Table](/readme-media/transmission-log-table.jpeg "Log Table")
- Transmission attempts page URL (http://magento2.docker/erp_sync/items/status)

- Transmission CLI command will in docker container, first you need ssh into container via `docker exec -it magento2-order-async_apache_1 bash` then run `bin/magento erp:tranmission --success=1` for sucessfull case and `bin/magento erp:tranmission --success=0` for failed case 



  

