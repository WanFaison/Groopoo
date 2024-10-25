Before launching the code, navigate to the '.env' file in your Symfony back-end and check the database configuration 'DATABASE_URL'

In case you use a MariaDB server: DATABASE_URL="mysql://username:password@127.0.0.1:3306/band_it?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
for example: DATABASE_URL="mysql://root:root@127.0.0.1:3306/band_it?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

In case you use a MySQL server: DATABASE_URL="mysql://username:password@127.0.0.1:3306/band_it?serverVersion=16&charset=utf8"
for example: DATABASE_URL="mysql://root:root@127.0.0.1:3306/band_it?serverVersion=16&charset=utf8"

NB:- ensure the 'serverVersion' corresponds to yours e.g ?serverVersion=mariadb-10.4.32
    - if needed, check the server version from the phpMyAdmin dashboard

ALSO, download(from the link below) and add the 'node_module' folder to the front and 'jwt' folder to the back\config. They contain all angular/typescript dependencies and public/private keys respectively required for the webapp:
https://drive.google.com/file/d/1rIF-dkQMbE-6F_PDgYbVGEBa2ddDUzVS/view?usp=drive_link
