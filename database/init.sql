-- StreamCSV sample schema and seed data
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) NOT NULL,
  `customer_address` text NOT NULL,
  `order_amount` double(10,2) NOT NULL,
  `created_time` time NOT NULL,
  `created_date` date NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`customer_name`, `customer_address`, `order_amount`, `created_time`, `created_date`) VALUES
('Mr Rahim', 'Magura, Bangladesh', 142.74, '04:04:04', '2022-12-25'),
('Ms Karim', 'Dhaka, Bangladesh', 89.50, '10:15:00', '2023-01-10'),
('Ayesha Khan', 'Chittagong, Bangladesh', 210.00, '14:22:10', '2023-02-05'),
('Imran Hossain', 'Sylhet, Bangladesh', 55.25, '09:05:42', '2023-03-18'),
('Nadia Islam', 'Rajshahi, Bangladesh', 320.10, '18:45:30', '2023-04-02');
