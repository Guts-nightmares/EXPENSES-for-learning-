docker run -d --name expenses_mariadb -e MYSQL_ROOT_PASSWORD=rootpass -e MYSQL_USER=expenses_user -e MYSQL_PASSWORD=expenses_pass -e MYSQL_DATABASE=expenses_db -p 3306:3306 mariadb:10.11
docker exec expenses_mariadb hostname -I
docker run -d --name expenses_php -p 8080:80 -v ./:/var/www/html php:8.2-apache
docker exec expenses_php docker-php-ext-install pdo pdo_mysql
# Mettre l'IP dans config/database.php a la place de 172.17.0.2
docker exec expenses_mariadb mysql -uroot -prootpass expenses_db -e "CREATE TABLE IF NOT EXISTS expenses (id INT AUTO_INCREMENT PRIMARY KEY, description VARCHAR(255) NOT NULL, amount DECIMAL(10,2) NOT NULL, category VARCHAR(50) NOT NULL, date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP);"
http://localhost:8080/public
