-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 17, 2024 at 02:14 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopping`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) DEFAULT NULL,
  `role` enum('SuperAdmin','NormalAdmin') DEFAULT 'NormalAdmin',
  `email` varchar(255) DEFAULT NULL,
  `Status` varchar(55) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `creationDate`, `updationDate`, `role`, `email`, `Status`) VALUES
(1001, 'admin', '123', '2017-01-24 16:21:18', '21-06-2018 08:27:55 PM', 'SuperAdmin', '1234@gmail.com', 'Active'),
(1002, 'CK (Admin)', '123', '2024-06-17 11:20:40', NULL, 'NormalAdmin', '123@gmail.com', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(255) DEFAULT NULL,
  `categoryDescription` longtext DEFAULT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) DEFAULT NULL,
  `Status` varchar(55) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `categoryName`, `categoryDescription`, `creationDate`, `updationDate`, `Status`) VALUES
(2001, 'Bedroom', 'Bedroom furnitures such as bedframe, wardrobes', '2024-01-24 11:17:37', '30-01-2024 12:22:24 AM', 'Active'),
(2002, 'Living Room', 'Living room furnitures such as sofas, coffee tables', '2024-01-24 11:19:32', '', 'Active'),
(2003, 'Dining Room', 'dining table, chair', '2024-01-24 11:19:54', '', 'Active'),
(2004, 'Office', 'office table', '2024-01-24 11:20:54', '', 'Active'),
(2005, 'Hallway and Entry', 'shoes rack, hallway benchees', '2024-01-24 11:20:54', '', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `orderId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`orderId`, `productId`, `quantity`, `rate`) VALUES
(3001, 6029, 1, 1),
(3001, 6080, 1, 1),
(3002, 6055, 1, 0),
(3003, 6016, 1, 0),
(3004, 6077, 1, 0),
(3005, 6047, 1, 0),
(3005, 6071, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `orderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `paymentMethod` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `orderStatus` varchar(55) DEFAULT NULL,
  `subtotal` float(10,2) DEFAULT 0.00,
  `shippingCharge` float(10,2) DEFAULT 0.00,
  `grandtotal` float(10,2) DEFAULT 0.00,
  `shippingReceiver` varchar(255) DEFAULT NULL,
  `shippingPhone` varchar(20) DEFAULT NULL,
  `shippingAddress` varchar(255) DEFAULT NULL,
  `billingReceiver` varchar(255) DEFAULT NULL,
  `billingPhone` varchar(20) DEFAULT NULL,
  `billingAddress` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `userId`, `orderDate`, `paymentMethod`, `payment_reference`, `orderStatus`, `subtotal`, `shippingCharge`, `grandtotal`, `shippingReceiver`, `shippingPhone`, `shippingAddress`, `billingReceiver`, `billingPhone`, `billingAddress`) VALUES
(3001, 9001, '2024-06-17 11:15:44', 'Stripe Checkout', NULL, 'Delivered', 189.98, 100.00, 289.98, 'Chwan Kai', '+60167406628', 'No.1, Jalan MMU, Ayer Keroh, Alor Gajah, Melaka, 51400', 'Chwan Kai', '+60167406628', 'No.15, Jalan Bestari 24, Johor Bahru, Johor, 81300'),
(3002, 9001, '2024-06-17 11:17:13', 'Stripe Checkout', NULL, 'In Process', 39.99, 150.00, 189.99, 'Abu Bakar', '+60167403366', 'Wisma KL, Jalan Hang Jebat, Kuala Lumpur, Kuala Lumpur, 13000', 'Abu Bakar', '+60167403366', 'Wisma KL, Jalan Hang Jebat, Kuala Lumpur, Kuala Lumpur, 13000'),
(3003, 9001, '2024-06-17 11:18:06', 'e-Wallet', NULL, 'Order Placed', 129.99, 150.00, 279.99, 'Abu Bakar', '+60167403366', 'Wisma KL, Jalan Hang Jebat, Kuala Lumpur, Kuala Lumpur, 13000', 'Abu Bakar', '+60167403366', 'Wisma KL, Jalan Hang Jebat, Kuala Lumpur, Kuala Lumpur, 13000'),
(3004, 9001, '2024-06-17 11:19:00', NULL, NULL, NULL, 79.99, 200.00, 279.99, 'Muthu', '+60167403399', '3, Jalan Sarawak 2, Bintulu, Sarawak, 67059', 'Muthu', '+60167403399', '3, Jalan Sarawak 2, Bintulu, Sarawak, 67059'),
(3005, 9002, '2024-06-17 11:28:31', 'Stripe Checkout', NULL, 'Cancelled', 119.98, 150.00, 269.98, 'CK Test account', '+601111111111', 'Jalan Maluri, Taman Conot, Kajang, Selangor, 28304', 'CK Test account', '+601111111111', 'Jalan Maluri, Taman Conot, Kajang, Selangor, 28304');

-- --------------------------------------------------------

--
-- Table structure for table `ordertrackhistory`
--

CREATE TABLE `ordertrackhistory` (
  `id` int(11) NOT NULL,
  `orderId` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `remark` mediumtext DEFAULT NULL,
  `postingDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ordertrackhistory`
--

INSERT INTO `ordertrackhistory` (`id`, `orderId`, `status`, `remark`, `postingDate`) VALUES
(4001, 3001, 'In Process', 'Warehouse preparing to pack and ship out.', '2024-06-17 11:21:30'),
(4002, 3001, 'Delivered', 'Delivered to the address.', '2024-06-17 11:21:44'),
(4003, 3002, 'In Process', 'Warehouse is preparing to pack and ship out.', '2024-06-17 11:22:05'),
(4004, 3005, 'Cancelled', 'Test order, cancelled and approved by manager.', '2024-06-17 11:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `productreviews`
--

CREATE TABLE `productreviews` (
  `id` int(11) NOT NULL,
  `productId` int(11) DEFAULT NULL,
  `quality` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `review` longtext DEFAULT NULL,
  `reviewDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `productreviews`
--

INSERT INTO `productreviews` (`id`, `productId`, `quality`, `price`, `value`, `name`, `summary`, `review`, `reviewDate`) VALUES
(5001, 6029, 5, 3, 5, 'Chwan Kai', 'BEST BEDSHEET EVER!!', 'This bedsheet is literally the best bedsheet I ever had! Sort yet smooth, MUST TRY!! It will be better if price is lower tho.', '2024-06-17 11:24:03'),
(5002, 6080, 1, 2, 3, 'Chwan Kai', 'NOT RECOMMEND', 'The products comes in broke condition. Quality seems very bad.', '2024-06-17 11:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `subCategory` int(11) DEFAULT NULL,
  `productName` varchar(255) DEFAULT NULL,
  `productCompany` varchar(255) DEFAULT NULL,
  `productPrice` decimal(10,2) DEFAULT NULL,
  `productPriceBeforeDiscount` decimal(10,2) DEFAULT NULL,
  `productDescription` longtext DEFAULT NULL,
  `productImage1` varchar(255) DEFAULT NULL,
  `productImage2` varchar(255) DEFAULT NULL,
  `productImage3` varchar(255) DEFAULT NULL,
  `productAvailability` varchar(255) DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) DEFAULT NULL,
  `Quantity` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category`, `subCategory`, `productName`, `productCompany`, `productPrice`, `productPriceBeforeDiscount`, `productDescription`, `productImage1`, `productImage2`, `productImage3`, `productAvailability`, `postingDate`, `updationDate`, `Quantity`) VALUES
(6001, 2001, 7001, 'Modern Sleep Platform Bed Frame', 'Dreamy Beds', 759.99, 900.00, '<span style=\"color: rgb(13, 13, 13); font-family: Söhne, ui-sans-serif, system-ui, -apple-system, &quot;Segoe UI&quot;, Roboto, Ubuntu, Cantarell, &quot;Noto Sans&quot;, sans-serif, &quot;Helvetica Neue&quot;, Arial, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;; font-size: 16px; white-space-collapse: preserve;\">Experience unparalleled comfort and style with our Modern Sleep Platform Bed Frame. Crafted with premium materials and designed for durability, this bed frame offers a sleek and minimalist aesthetic that complements any bedroom decor.</span><br>', 'ramnefjaell-upholstered-bed-frame-kilanda-light-beige-luroey__1258172_pe927371_s5.avif', 'ramnefjaell-upholstered-bed-frame-kilanda-light-beige-luroey__1258175_pe927363_s5.avif', 'ramnefjaell-upholstered-bed-frame-kilanda-light-beige-luroey__1258195_pe927382_s5.avif', 'In Stock', '2024-05-20 20:55:50', NULL, 33),
(6002, 2001, 7001, 'Classic Metal Bed Frame', 'Comfort Dreams Furniture', 600.00, 399.99, 'Transform your bedroom into a luxurious retreat with the Sierra Upholstered Bed Frame. Featuring elegant upholstery and a sturdy wooden frame, this bed frame combines sophistication with comfort for a restful nights sleep.', 'leirvik-bed-frame-white-luroey__1074367_pe856167_s5.avif', 'leirvik-bed-frame-white-luroey__1102030_pe866852_s5.avif', 'leirvik-bed-frame-white-luroey__1102031_pe866853_s5.avif', 'In Stock', '2024-05-20 21:10:49', NULL, 35),
(6003, 2001, 7001, 'Sleek Leather Platform Bed', 'Comfort Dreams Furniture', 499.99, 399.99, 'Achieve a minimalist look with our Sleek Leather Platform Bed. Crafted from high-quality leather and sleek metal accents, this bed frame offers style and comfort in one sleek package.', 'grimsbu-bed-frame-grey__0749249_pe747240_s5.avif', 'grimsbu-bed-frame-grey__1101968_pe866785_s5.avif', 'grimsbu-bed-frame-grey__1101969_pe866786_s5.avif', 'In Stock', '2024-05-20 21:17:10', NULL, 34),
(6004, 2001, 7001, 'Luna Storage Bed Frame', 'Vintage Beds Ltd', 499.99, 399.99, 'Add a touch of nostalgia to your bedroom with our Classic Metal Bed Frame. Crafted with timeless design and durable metal construction, this bed frame offers both style and stability for years to come.', 'malm-bed-frame-high-w-2-storage-boxes-white-luroey__1154393_pe886042_s5.avif', 'malm-bed-frame-high-w-2-storage-boxes-white-luroey__1101597_pe866769_s5.avif', 'malm-bed-frame-high-w-2-storage-boxes-white-luroey__1101598_pe866682_s5.avif', 'In Stock', '2024-05-20 21:17:10', NULL, 13),
(6005, 2001, 7001, 'Coastal Wood Bed Frame', 'Space-Saving Solutions Co', 150.00, 150.00, 'Maximize your bedroom space with the Luna Storage Bed Frame. Featuring built-in drawers and a stylish design, this bed frame offers ample storage without compromising on style or comfort.', 'slattum-upholstered-bed-frame-vissle-dark-grey__1259335_pe926650_s5.avif', 'slattum-upholstered-bed-frame-vissle-dark-grey__1259363_pe926663_s5.avif', 'slattum-upholstered-bed-frame-vissle-dark-grey__1259338_pe926654_s5.avif', 'In Stock', '2024-05-20 21:17:10', NULL, 45),
(6006, 2001, 7001, 'Coastal Wood Bed Frame', 'Seaside Furniture Creations', 200.99, 300.99, 'Bring the charm of the coast to your bedroom with our Coastal Wood Bed Frame. Crafted from solid wood and featuring a rustic finish, this bed frame exudes seaside vibes and timeless appeal for a relaxing atmosphere.', 'nesttun-bed-frame-white-luroey__0637599_pe698415_s5.avif', 'nesttun-bed-frame-white-luroey__1101970_pe866787_s5.avif', 'nesttun-bed-frame-white-luroey__1101971_pe866810_s5.avif', 'In Stock', '2024-05-20 21:17:10', NULL, 35),
(6007, 2001, 7002, 'Classic Oak Bedside Table', 'Heritage Furniture Co.', 199.99, 249.99, 'Complete your bedroom ensemble with our Classic Oak Bedside Table. Crafted from solid oak, this bedside table offers timeless style and convenient storage for your nighttime essentials.', 'knarrevik-bedside-table-black__1255280_pe924479_s5.avif', 'knarrevik-bedside-table-black__1295970_pe935630_s5.webp ', 'knarrevik-bedside-table-black__1295971_pe935631_s5.avif', 'In Stock', '2024-05-20 21:24:56', NULL, 35),
(6008, 2001, 7002, 'Modern Glass Nightstand', 'Contemporary Living Designs', 149.99, 179.99, 'Add a touch of contemporary flair to your bedroom with our Modern Glass Nightstand. Featuring sleek glass panels and metal accents, this nightstand combines style with functionality.', 'eket-wall-cabinet-with-glass-door-dark-grey__0807286_pe770379_s5.avif', 'eket-wall-cabinet-with-glass-door-dark-grey__0807285_pe770358_s5.jpg', 'eket-wall-cabinet-with-glass-door-dark-grey__0807284_pe770380_s5.avif', 'In Stock', '2024-05-20 21:24:56', NULL, 25),
(6009, 2001, 7002, 'Rustic Pine Bedside Cabinet', 'Country Home Furnishings', 129.99, 159.99, 'Bring rustic charm to your bedside with our Rustic Pine Bedside Cabinet. Crafted from solid pine wood, this bedside cabinet features ample storage space and a weathered finish for a cozy look.', 'hattasen-bedside-table-shelf-unit-white__1111044_pe870647_s5.avif', 'hattasen-bedside-table-shelf-unit-white__1111041_pe870650_s5.avif', 'hattasen-bedside-table-shelf-unit-white__1111039_pe870645_s5.avif', 'In Stock', '2024-05-20 21:24:56', NULL, 76),
(6010, 2001, 7002, 'Industrial Metal Nightstand', 'Urban Loft Creations', 179.99, 209.99, 'Enhance your bedroom decor with our Industrial Metal Nightstand. Constructed from sturdy metal and featuring a distressed finish, this nightstand adds an industrial touch to any space.', 'gladom-tray-table-black__0567223_pe664991_s5.avif', 'gladom-tray-table-black__0837078_pe664995_s5.avif', 'gladom-tray-table-black__0837072_pe664992_s5.avif', 'In Stock', '2024-05-20 21:24:56', NULL, 24),
(6011, 2001, 7002, 'Elegant Marble Bedside Table', 'Luxury Living Designs', 299.99, 349.99, 'Upgrade your bedside with our Elegant Marble Bedside Table. Crafted from luxurious marble and featuring gold accents, this bedside table exudes opulence and sophistication.', 'hauga-bedside-table-beige__0963762_pe808702_s5.avif', 'hauga-bedside-table-beige__1010403_pe828049_s5.avif', 'hauga-bedside-table-beige__1010404_pe828050_s5.avif', 'In Stock', '2024-05-20 21:24:56', NULL, 10),
(6012, 2001, 7003, 'Vintage Vanity Table', 'Antique Treasures Ltd', 399.99, 449.99, 'Create a charming makeup station with our Vintage Vanity Table. This includes a beautifully crafted vanity table, perfect for adding elegance to your bedroom.', 'malm-dressing-table-white__0627084_pe693164_s5.avif', 'malm-dressing-table-white__0735339_pe739894_s5.webp', 'malm-dressing-table-white__0287238_pe413841_s5.avif', 'In Stock', '2024-05-20 21:36:30', NULL, 24),
(6013, 2001, 7003, 'Modern Makeup Vanity Desk', 'Urban Chic Designs', 279.99, 319.99, 'Get ready in style with our Modern Makeup Vanity Desk. Featuring sleek lines and ample storage, this vanity desk provides the perfect space to organize your beauty essentials.', 'hemnes-dressing-table-with-mirror-white__0627085_pe693165_s5.avif', 'hemnes-dressing-table-with-mirror-white__1151404_pe886162_s5.avif', 'hemnes-dressing-table-with-mirror-white__0735324_pe739880_s5.avif', 'In Stock', '2024-05-20 21:36:30', NULL, 44),
(6014, 2001, 7003, 'Luxury Dressing Table and Stool', 'Royal Furniture Emporium', 599.99, 699.99, 'Indulge in luxury with our Luxury Dressing Table and Stool. Crafted from high-quality materials and adorned with intricate details, this dressing table set adds glamour to any bedroom.', 'skruvsta-swivel-chair-ysane-white__0724712_pe734595_s5.avif', 'skruvsta-swivel-chair-ysane-white__0798308_ph165495_s5.webp', 'skruvsta-swivel-chair-ysane-white__0798310_ph165494_s5.avif', 'In Stock', '2024-05-20 21:36:30', NULL, 32),
(6015, 2001, 7004, 'Modern Coat Stand', 'Contemporary Living Solutions', 89.99, 99.99, 'Keep your hallway organized with our Modern Coat Stand. Featuring a minimalist design and sturdy construction, this coat stand offers a stylish solution for hanging coats, hats, and umbrellas.', 'bondskaeret-hat-and-coat-stand-black__1176355_pe894994_s5.avif', 'bondskaeret-hat-and-coat-stand-black__1213404_ph193716_s5.jpg', 'bondskaeret-hat-and-coat-stand-black__1176274_pe894978_s5.avif', 'In Stock', '2024-05-20 21:40:22', NULL, 34),
(6016, 2001, 7004, 'Freestanding Mirror Stand', 'Reflections Furniture', 129.99, 149.99, 'Add functionality and style to your bedroom with our Freestanding Mirror Stand. This mirror stand features a full-length mirror and a sleek stand, making it perfect for getting ready in the morning.', 'knapper-standing-mirror-white__0594294_pe676198_s5.avif', 'knapper-standing-mirror-white__0858698_pe676200_s5.avif', 'knapper-standing-mirror-white__0715798_pe730620_s5.avif', 'In Stock', '2024-05-20 21:40:22', NULL, 74),
(6017, 2001, 7005, 'Vintage Wooden Wardrobe', 'Heritage Furniture Co.', 699.99, 799.99, 'Organize your clothes in style with our Vintage Wooden Wardrobe. Crafted from solid wood and featuring intricate details, this wardrobe adds charm and functionality to any bedroom.', 'hemnes-glass-door-cabinet-with-3-drawers-white-stain-light-brown__0805255_pe769478_s5.avif', 'hemnes-glass-door-cabinet-with-3-drawers-white-stain-light-brown__1052143_pe845952_s5.avif', 'hemnes-glass-door-cabinet-with-3-drawers-white-stain-light-brown__0849644_pe671192_s5.avif', 'In Stock', '2024-05-20 21:44:46', NULL, 34),
(6018, 2001, 7005, 'Modern Sliding Door Wardrobe', 'Urban Living Designs', 899.99, 999.99, 'Maximize your bedroom space with our Modern Sliding Door Wardrobe. With sleek sliding doors and ample storage space, this wardrobe combines style and functionality effortlessly.', 'hauga-storage-combination-white__0914344_pe783957_s5.avif', 'hauga-storage-combination-white__0931887_pe791287_s5.avif', 'hauga-storage-combination-white__1060471_ph177897_s5.avif', 'In Stock', '2024-05-20 21:44:46', NULL, 45),
(6019, 2001, 7005, 'Rustic Chest of Drawers', 'Country Charm Furniture', 549.99, 649.99, 'Bring rustic charm to your bedroom with our Rustic Chest of Drawers. Crafted from reclaimed wood and featuring a distressed finish, this chest of drawers adds character to any space.', 'hemnes-shoe-cabinet-with-4-compartments-white__0710742_pe727760_s5.avif', 'hemnes-shoe-cabinet-with-4-compartments-white__1130704_pe877933_s5.avif', 'hemnes-shoe-cabinet-with-4-compartments-white__0397340_pe562473_s5.avif', 'In Stock', '2024-05-20 21:44:46', NULL, 24),
(6020, 2001, 7006, 'Elegant Oak Wardrobe', 'Timeless Furniture Co', 899.99, 1099.99, 'Elevate your bedroom with our Elegant Oak Wardrobe. Featuring a classic design and ample storage space, this wardrobe is crafted from high-quality oak for lasting durability and beauty.', 'kleppstad-wardrobe-with-2-doors-white__0733324_pe748781_s5.avif', 'kleppstad-wardrobe-with-2-doors-white__0733323_pe748780_s5.avif', 'kleppstad-wardrobe-with-2-doors-white__0813670_ph165843_s5.avif', 'In Stock', '2024-05-21 04:35:28', NULL, 53),
(6021, 2001, 7006, 'Modern Oak Wardrobe', 'Urban Style Interiors', 999.99, 1199.99, 'Add a touch of modern sophistication to your bedroom with our Modern Mirror Wardrobe. With sleek mirrored doors and a spacious interior, this wardrobe combines style and practicality.', 'smastad-wardrobe-white-birch-with-2-clothes-rails__0925750_pe788862_s5.avif', 'smastad-wardrobe-white-birch-with-2-clothes-rails__0936463_pe793260_s5.avif', 'smastad-wardrobe-white-birch-with-2-clothes-rails__1268283_pe928676_s5.avif', 'In Stock', '2024-05-21 04:35:28', NULL, 34),
(6022, 2001, 7006, 'Classic Pine Wardrobe', 'Heritage Home Furnishings', 749.99, 899.99, 'Our Classic Pine Wardrobe offers a timeless design and ample storage. Crafted from solid pine wood, this wardrobe is both sturdy and stylish, making it a perfect addition to any bedroom.', 'brimnes-wardrobe-with-3-doors-white__0176787_pe329567_s5.avif', 'brimnes-wardrobe-with-3-doors-white__0501302_ph136365_s5.avif', 'brimnes-wardrobe-with-3-doors-white__0746976_pe744295_s5.avif', 'In Stock', '2024-05-21 04:35:28', NULL, 24),
(6023, 2001, 7006, 'Luxury Sliding Door Wardrobe', 'Premium Furniture Ltd.', 1299.99, 1499.99, 'Experience the ultimate in luxury with our Luxury Sliding Door Wardrobe. Featuring sleek sliding doors and a modern design, this wardrobe offers ample storage space for all your clothing and accessories.', 'pax-hasvik-wardrobe-white-stained-oak-effect-white-stained-oak-effect__1119871_pe873632_s5.avif', 'pax-hasvik-wardrobe-white-stained-oak-effect-white-stained-oak-effect__1184490_pe897929_s5.avif', 'pax-hasvik-wardrobe-white-stained-oak-effect-white-stained-oak-effect__1182211_pe897068_s5.avif', 'In Stock', '2024-05-21 04:35:28', NULL, 34),
(6024, 2001, 7006, 'Compact Wardrobe with Shelves', 'Space Savers Co', 649.99, 799.99, 'Maximize your storage space with our Compact Wardrobe with Shelves. Designed for smaller bedrooms, this wardrobe features multiple shelves and a hanging rod for optimal organization.', 'rakkestad-wardrobe-with-3-doors-black-brown__0823987_pe776018_s5.avif', 'rakkestad-wardrobe-with-3-doors-black-brown__0823988_pe776019_s5.avif', 'rakkestad-wardrobe-with-3-doors-black-brown__0823989_pe776020_s5.avif', 'In Stock', '2024-05-21 04:35:28', NULL, 34),
(6025, 2001, 7007, 'Supreme Comfort Sprung Mattress', 'DreamSleep Mattresses', 499.99, 599.99, 'Experience unmatched comfort with our Supreme Comfort Sprung Mattress. Featuring advanced spring technology for optimal support and a plush top layer for a restful sleep.', 'vestmarka-sprung-mattress-extra-firm-light-blue__1150828_pe884909_s5.avif', 'vestmarka-sprung-mattress-extra-firm-light-blue__1044455_pe842169_s5.avif', 'vestmarka-sprung-mattress-extra-firm-light-blue__1155951_pe886826_s5.avif', 'In Stock', '2024-05-21 04:39:10', NULL, 23),
(6026, 2001, 7007, 'Luxury Plush Sprung Mattress', 'Elite Bedding Co.', 699.99, 799.99, 'Indulge in luxury with our Luxury Plush Sprung Mattress. Designed with high-quality springs and a soft, plush surface, this mattress offers superior comfort and support.', 'vadsoe-sprung-mattress-extra-firm-light-blue__1077760_pe856997_s5.avif', 'vadsoe-sprung-mattress-extra-firm-light-blue__0928310_pe789775_s5.avif', 'vadsoe-sprung-mattress-extra-firm-light-blue__0954659_pe803417_s5.avif', 'In Stock', '2024-05-21 04:39:10', NULL, 24),
(6027, 2001, 7007, 'Orthopedic Sprung Mattress', 'HealthSleep Solutions', 599.99, 699.99, 'Our Orthopedic Sprung Mattress provides excellent support for your spine and joints. Perfect for those seeking a firm and supportive mattress for a healthy sleep posture.', 'valevag-pocket-sprung-mattress-firm-light-blue__1150857_pe884903_s5.avif', 'valevag-pocket-sprung-mattress-firm-light-blue__1044460_pe842180_s5.avif', 'valevag-pocket-sprung-mattress-firm-light-blue__1155979_pe886828_s5.avif', 'In Stock', '2024-05-21 04:39:10', NULL, 24),
(6028, 2001, 7008, 'Premium Cotton Bedsheet Set', 'Comfort Linen', 89.99, 119.99, 'Our Premium Cotton Bedsheet Set is made from 100% high-quality cotton, ensuring softness and durability. Available in various colors and patterns to match your bedroom decor.', 'dvala-fitted-sheet-white__0604085_pe681026_s5.avif', 'dvala-fitted-sheet-white__1034274_pe837594_s5.avif', 'dvala-fitted-sheet-white__1034363_pe837680_s5.avif', 'In Stock', '2024-05-21 04:42:03', NULL, 43),
(6029, 2001, 7008, 'Silky Satin Bedsheet Collection', 'Luxury Bedding Co.', 129.99, 159.99, 'Add a touch of luxury to your bedroom with our Silky Satin Bedsheet Collection. These bedsheets are crafted from the finest satin, providing a silky smooth feel and a beautiful sheen.', 'askloenn-fitted-sheet-white-cherry-blossom-branch__0993722_pe820674_s5.avif', 'askloenn-fitted-sheet-white-cherry-blossom-branch__0993724_pe820676_s5.avif', 'askloenn-fitted-sheet-white-cherry-blossom-branch__0993723_pe820677_s5.avif', 'In Stock', '2024-05-21 04:42:03', NULL, 23),
(6030, 2002, 7009, 'Classic Leather Armchair', 'Elegance Furniture Co.', 799.99, 999.99, 'Experience ultimate comfort and style with our Classic Leather Armchair. Made from high-quality leather and featuring a timeless design, this armchair is perfect for any living space.', 'ekenaeset-armchair-kilanda-light-beige__1109687_pe870153_s5.avif', 'ekenaeset-armchair-kilanda-light-beige__1179060_pe895831_s5.avif', 'ekenaeset-armchair-kilanda-light-beige__1110707_pe870568_s5.avif', 'In Stock', '2024-05-21 04:49:07', NULL, 10),
(6031, 2002, 7009, 'Modern Fabric Armchair', 'Contemporary Living', 499.99, 649.99, 'Our Modern Fabric Armchair offers a sleek and stylish design, perfect for modern interiors. Upholstered in durable fabric, this armchair provides both comfort and elegance.', 'poaeng-armchair-birch-veneer-knisa-light-beige__0571500_pe666933_s5.avif', 'poaeng-armchair-birch-veneer-knisa-light-beige__0837298_pe666936_s5.avif', 'poaeng-armchair-birch-veneer-knisa-light-beige__0837295_pe666935_s5.avif', 'In Stock', '2024-05-21 04:49:07', NULL, 7),
(6032, 2002, 7009, 'Recliner Armchair', 'Comfort Seating', 699.99, 899.99, 'Relax in luxury with our Recliner Armchair. Featuring a reclining function and plush padding, this armchair is designed for maximum comfort and relaxation.', 'muren-recliner-remmarn-dark-grey__0908536_pe783303_s5.avif', 'muren-recliner-remmarn-dark-grey__0908535_pe783268_s5.avif', 'muren-recliner-remmarn-dark-grey__0908538_pe783271_s5.webp', 'In Stock', '2024-05-21 04:49:07', NULL, 24),
(6033, 2002, 7009, 'Sleek Modern Armchair', 'Urban Elegance', 549.99, 749.99, 'The Sleek Modern Armchair brings a touch of contemporary style to any room. With its clean lines and minimalist design, this armchair is both stylish and comfortable.', 'herrakra-armchair-skulsta-black__1213663_pe911199_s5.avif', 'herrakra-armchair-skulsta-black__1213662_pe911198_s5.avif', 'herrakra-armchair-skulsta-black__1213661_pe911197_s5.avif', 'In Stock', '2024-05-21 04:49:08', NULL, 32),
(6034, 2002, 7010, 'Comfort Plus Two-Seat Sofa', 'Cozy Home Furnishings', 899.99, 1199.99, 'Experience ultimate comfort with the Comfort Plus Two-Seat Sofa, featuring plush cushions and a durable frame for long-lasting relaxation.', 'glostad-2-seat-sofa-knisa-dark-grey__0950864_pe800736_s5.avif', 'glostad-2-seat-sofa-knisa-dark-grey__1059523_ph180677_s5.avif', 'glostad-2-seat-sofa-knisa-dark-grey__0987393_pe817515_s5.avif', 'In Stock', '2024-05-21 04:53:46', NULL, 34),
(6035, 2002, 7010, 'Modern Luxe Two-Seat Sofa', 'Urban Living', 1049.99, 1399.99, 'The Modern Luxe Two-Seat Sofa offers a sleek design with premium upholstery, perfect for contemporary living spaces.', 'klippan-2-seat-sofa-vissle-grey__0239990_pe379591_s5.avif', 'klippan-2-seat-sofa-vissle-grey__0820948_pe709146_s5.avif', 'klippan-2-seat-sofa-vissle-grey__0820907_pe709145_s5.avif', 'In Stock', '2024-05-21 04:53:46', NULL, 23),
(6036, 2002, 7010, 'Classic Comfort Three-Seat Sofa', 'Heritage Furniture Co.', 1249.99, 1699.99, 'The Classic Comfort Three-Seat Sofa combines traditional design with modern comfort, featuring high-quality materials and craftsmanship.', 'ektorp-3-seat-sofa-hakebo-dark-grey__1194847_pe902097_s5.avif', 'ektorp-3-seat-sofa-hakebo-dark-grey__1194846_pe902096_s5.avif', 'ektorp-3-seat-sofa-hakebo-dark-grey__1194839_pe902073_s5.avif', 'In Stock', '2024-05-21 04:53:46', NULL, 34),
(6037, 2002, 7010, 'Urban Chic Three-Seat Sofa', 'City Living', 1399.99, 1899.99, 'Enhance your living room with the Urban Chic Three-Seat Sofa, designed with contemporary aesthetics and unmatched comfort.', 'kivik-3-seat-sofa-tibbleby-beige-grey__1056144_pe848277_s5.avif', 'kivik-3-seat-sofa-tibbleby-beige-grey__1056143_pe848278_s5.avif', 'kivik-3-seat-sofa-tibbleby-beige-grey__1056136_pe848268_s5.avif', 'In Stock', '2024-05-21 04:53:46', NULL, 40),
(6038, 2002, 7010, 'Luxe Corner Sofa', 'Elite Interiors', 1999.99, 2499.99, 'The Luxe Corner Sofa offers ample seating with a modern design, perfect for spacious living rooms and family gatherings.', 'friheten-corner-sofa-bed-with-storage-skiftebo-dark-grey__0175610_pe328883_s5.avif', 'friheten-corner-sofa-bed-with-storage-skiftebo-dark-grey__0779005_ph163058_s5.webp', 'friheten-corner-sofa-bed-with-storage-skiftebo-dark-grey__0779007_ph163064_s5.avif', 'In Stock', '2024-05-21 04:53:46', NULL, 23),
(6039, 2002, 7011, 'White Classic Coffee Table', 'Elegance Home', 299.99, 399.99, 'The White Classic Coffee Table brings a touch of timeless elegance to your living space with its pristine finish and sturdy build.', 'lack-coffee-table-white__0702218_pe724350_s5.avif', 'lack-coffee-table-white__0702217_pe724349_s5.avif', 'lack-coffee-table-white__0836361_pe601397_s5.avif', 'In Stock', '2024-05-21 04:57:52', NULL, 23),
(6040, 2002, 7011, 'Modern Black Coffee Table', 'Contemporary Designs', 349.99, 449.99, 'Featuring a sleek and minimalist design, the Modern Black Coffee Table is perfect for modern living rooms, providing both style and functionality.', 'vittsjoe-coffee-table-black-brown-glass__0135348_pe292039_s5.avif', 'vittsjoe-coffee-table-black-brown-glass__0836655_pe601386_s5.avif', 'vittsjoe-coffee-table-black-brown-glass__1058803_ph181553_s5.avif', 'In Stock', '2024-05-21 04:57:52', NULL, 10),
(6041, 2002, 7011, 'Oak Coffee Table', 'Rustic Charm Furnishings', 399.99, 499.99, 'Crafted from high-quality oak, this coffee table adds a touch of rustic charm to your home while offering durability and ample surface space.', 'borgeby-coffee-table-birch-veneer__0987623_pe817609_s5.avif', 'borgeby-coffee-table-birch-veneer__0949801_pe800020_s5.webp', 'borgeby-coffee-table-birch-veneer__0949800_pe800017_s5.avif', 'In Stock', '2024-05-21 04:57:52', NULL, 23),
(6042, 2002, 7012, 'White Classic TV Cabinet', 'Elegance Home', 499.99, 599.99, 'The White Classic TV Cabinet offers a blend of timeless elegance and practical storage, making it a perfect addition to any living room.', 'besta-tv-bench-with-doors-white-lappviken-white__0719188_pe731908_s5.avif', 'besta-tv-bench-with-doors-white-lappviken-white__0995907_pe821962_s5.avif', 'besta-tv-bench-with-doors-white-lappviken-white__0723576_pe734034_s5.avif', 'In Stock', '2024-05-21 05:02:11', NULL, 34),
(6043, 2002, 7012, 'Modern Black TV Cabinet with Door', 'Contemporary Designs', 549.99, 649.99, 'This sleek Modern Black TV Cabinet with a door provides ample storage while maintaining a minimalist design, ideal for modern homes.', 'besta-tv-bench-with-doors-black-brown-bjoerkoeviken-stubbarp-brown-stained-oak-veneer__0992037_pe819767_s5.avif', 'besta-tv-bench-with-doors-black-brown-bjoerkoeviken-stubbarp-brown-stained-oak-veneer__0999549_pe823617_s5.avif', 'besta-tv-bench-with-doors-black-brown-bjoerkoeviken-stubbarp-brown-stained-oak-veneer__1001968_pe824566_s5.avif', 'In Stock', '2024-05-21 05:02:11', NULL, 20),
(6044, 2002, 7012, 'Modern Black TV Cabinet with Glass', 'Contemporary Designs', 599.99, 699.99, 'Featuring a contemporary design with glass panels, this Modern Black TV Cabinet adds a touch of elegance to your entertainment area.', 'brimnes-tv-bench-black__0704610_pe725291_s5.avif', 'brimnes-tv-bench-black__0851278_pe725293_s5.avif', 'brimnes-tv-bench-black__0849973_pe725295_s5.avif', 'In Stock', '2024-05-21 05:02:11', NULL, 10),
(6045, 2002, 7012, 'Oak TV Cabinet', 'Rustic Charm Furnishings', 649.99, 749.99, 'Crafted from high-quality oak, this TV cabinet offers a rustic charm and durable storage solution for your living room.', 'besta-tv-bench-white-stained-oak-effect-lappviken-white__0343351_pe535931_s5.avif', 'besta-tv-bench-white-stained-oak-effect-lappviken-white__0353528_pe537013_s5.avif', 'besta-tv-bench-white-stained-oak-effect-lappviken-white__0843957_pe591118_s5.avif', 'In Stock', '2024-05-21 05:02:11', NULL, 30),
(6046, 2002, 7013, 'Red Tray Table', 'Vibrant Furniture Co.', 79.99, 99.99, 'The Red Tray Table adds a pop of color and functionality to your living space, perfect for snacks, drinks, or décor.', 'gladom-tray-table-red__0568691_pe665565_s5.avif', 'gladom-tray-table-red__1191496_pe900639_s5.jpg', 'gladom-tray-table-red__0568694_pe665568_s5.avif', 'In Stock', '2024-05-21 05:05:46', NULL, 20),
(6047, 2002, 7013, 'White Classic Side Table', 'Elegance Home', 89.99, 109.99, 'This White Classic Side Table offers timeless elegance and a practical surface for your bedside or living room needs.', 'lack-side-table-white__1057250_pe848800_s5.avif', 'lack-side-table-white__1063541_ph182201_s5.avif', 'lack-side-table-white__1063542_ph181543_s5.avifhol-side-table-acacia__0108769_pe258448_s5.avif', 'In Stock', '2024-05-21 05:05:46', NULL, 19),
(6048, 2002, 7013, 'Wooden Side Table', 'Rustic Charm Furnishings', 99.99, 129.99, 'The Wooden Side Table combines rustic charm with modern functionality, providing a durable and stylish addition to any room.', 'hol-side-table-acacia__0108769_pe258448_s5.avif', 'hol-side-table-acacia__0836247_pe601416_s5.avif', 'hol-side-table-acacia__0835866_pe583785_s5.avif', 'In Stock', '2024-05-21 05:05:46', NULL, 10),
(6049, 2002, 7014, 'Classic White 4x3 Shelf', 'Vintage Home Furnishings', 149.99, 179.99, 'The Classic White 4x3 Shelf offers ample storage space and timeless elegance, perfect for displaying books, photos, or décor.', 'kallax-shelving-unit-white__0601747_pe681619_s5.avif', 'kallax-shelving-unit-white__1051351_pe845166_s5.webp', 'kallax-shelving-unit-white__1084846_pe859909_s5.avif', 'In Stock', '2024-05-21 05:10:14', NULL, 30),
(6050, 2002, 7014, 'Classic White 4x2 Shelf', 'Elegance Home', 129.99, 159.99, 'The Classic White 4x2 Shelf provides versatile storage solutions with its classic design and sturdy construction.', 'kallax-shelving-unit-white__0644757_pe702939_s5.avif', 'kallax-shelving-unit-white__1084790_pe859876_s5.avif', 'kallax-shelving-unit-white__1084796_pe859882_s5.avif', 'In Stock', '2024-05-21 05:10:14', NULL, 20),
(6051, 2002, 7014, 'Black Metal with Wood Shelf', 'Modern Living Designs', 169.99, 199.99, 'The Black Metal with Wood Shelf combines industrial style with natural elements, offering a stylish and functional storage solution for your home.', 'fjaellbo-shelving-unit-black__1071961_pe855055_s5.webp', 'fjaellbo-shelving-unit-black__0849617_pe616391_s5.avif', 'fjaellbo-shelving-unit-black__1026873_pe834580_s5.avif', 'In Stock', '2024-05-21 05:10:14', NULL, 20),
(6052, 2002, 7015, 'Classic White Cabinet with Doors', 'Vintage Home Furnishings', 249.99, 299.99, 'The Classic White Cabinet with Doors offers stylish storage with its classic design and convenient doors, perfect for keeping your belongings organized and out of sight.', 'baggebo-cabinet-with-door-white__1016757_pe830615_s5.avif', 'baggebo-cabinet-with-door-white__1020932_pe832020_s5.avif', 'baggebo-cabinet-with-door-white__1094773_ph178209_s5.webp', 'In Stock', '2024-05-21 05:14:38', NULL, 30),
(6053, 2002, 7015, 'Modern Black Cabinet with Doors', 'Elegance Home', 279.99, 329.99, 'The Modern Black Cabinet with Doors combines sleek design with ample storage space, featuring convenient doors and a contemporary finish.', 'brimnes-glass-door-cabinet-black__0601743_pe681617_s5.webp', 'brimnes-glass-door-cabinet-black__1052133_pe845951_s5.avif', 'brimnes-glass-door-cabinet-black__0851545_pe689196_s5.avif', 'In Stock', '2024-05-21 05:14:38', NULL, 10),
(6054, 2002, 7015, 'Oak Cabinet with Doors', 'Nature Touch Furniture', 319.99, 369.99, 'The Oak Cabinet with Doors offers natural beauty and practical storage, featuring sturdy construction and stylish doors for a timeless addition to your home.', 'besta-storage-combination-with-doors-white-studsviken-kabbarp-white-woven-poplar__0994430_pe821087_s5.avif', 'besta-storage-combination-with-doors-white-studsviken-kabbarp-white-woven-poplar__0999265_pe823353_s5.avif', 'besta-storage-combination-with-doors-white-studsviken-kabbarp-white-woven-poplar__0999264_pe823389_s5.avif', 'In Stock', '2024-05-21 05:14:38', NULL, 18),
(6055, 2002, 7016, 'Rainbow Kids Chair', 'Playful Furniture Co.', 39.99, 49.99, 'The Rainbow Kids Chair adds a splash of color to any child room, providing a fun and comfortable seating option for playtime or study sessions.', 'poaeng-childrens-armchair-birch-veneer-medskog-dinosaur-pattern__0971808_pe811452_s5.avif', 'poaeng-childrens-armchair-birch-veneer-medskog-dinosaur-pattern__0971809_pe811451_s5.avif', 'poaeng-childrens-armchair-birch-veneer-medskog-dinosaur-pattern__0953717_pe802880_s5.webp', 'In Stock', '2024-05-21 05:18:15', NULL, 31),
(6056, 2003, 7017, 'Elegant Dining Set - Black', 'Modern Living Furnishings', 799.99, 899.99, 'The Elegant Dining Set in Black includes a sleek table and four matching chairs, perfect for contemporary dining spaces.', 'sandsberg-adde-table-and-4-chairs-black-black__1016431_pe830392_s5.avif', 'sandsberg-adde-table-and-4-chairs-black-black__1027680_pe834977_s5.avif', 'sandsberg-adde-table-and-4-chairs-black-black__1016429_pe830390_s5.avif', 'In Stock', '2024-05-21 05:21:05', NULL, 20),
(6057, 2003, 7017, 'Classic Dining Set - White', 'Timeless Home Interiors', 899.99, 999.99, 'The Classic Dining Set in White features a stylish table and six comfortable chairs, creating an inviting atmosphere for family meals and gatherings.', 'vangsta-adde-table-and-6-chairs-white-white__1097907_pe865185_s5.avif', 'vangsta-adde-table-and-6-chairs-white-white__1097908_pe865186_s5.avif', 'vangsta-adde-table-and-6-chairs-white-white__0870395_pe640683_s5.avif', 'In Stock', '2024-05-21 05:21:05', NULL, 10),
(6058, 2003, 7017, 'Rustic Dining Set - Wood', 'Country Charm Furniture', 699.99, 799.99, 'The Rustic Dining Set in Wood includes a charming table and four sturdy chairs, bringing warmth and character to your dining area.', 'voxloev-voxloev-table-and-4-chairs-bamboo-bamboo__0926660_pe789444_s5.avif', 'voxloev-voxloev-table-and-4-chairs-bamboo-bamboo__0926661_pe789443_s5.avif', 'voxloev-voxloev-table-and-4-chairs-bamboo-bamboo__0997394_ph176797_s5.avif', 'In Stock', '2024-05-21 05:21:05', NULL, 20),
(6059, 2003, 7018, 'Modern Dining Chair', 'Urban Home Designs', 149.99, 169.99, 'The Modern Dining Chair features a sleek design and comfortable cushioning, perfect for adding contemporary style to your dining space.', 'oestanoe-chair-deep-green-remmarn-deep-green__1209972_pe909498_s5.avif', 'oestanoe-chair-deep-green-remmarn-deep-green__1304071_ph197845_s5.avif', 'oestanoe-chair-deep-green-remmarn-deep-green__1304070_ph197846_s5.avif', 'In Stock', '2024-05-21 05:25:08', NULL, 30),
(6060, 2003, 7018, 'Luxury Dining Chair', 'Luxury Living Creations', 169.99, 189.99, 'The Luxury Dining Chair combines sophistication with comfort, featuring a classic design and plush upholstery for luxurious dining experiences.', 'krylbo-chair-tonerud-dark-beige__1208495_pe908606_s5.avif', 'krylbo-chair-tonerud-dark-beige__1259007_ph194892_s5.avif', 'krylbo-chair-tonerud-dark-beige__1208493_pe908599_s5.avif', 'In Stock', '2024-05-21 05:25:08', NULL, 20),
(6061, 2003, 7018, 'Rustic Dining Chair', 'Farmhouse Furnishings Co.', 129.99, 149.99, 'The Rustic Dining Chair exudes charm and character, crafted from sturdy wood with a distressed finish for a cozy farmhouse feel.', 'adde-chair-white__0728280_pe736170_s5.avif', 'adde-chair-white__0872085_pe594884_s5.avif', 'adde-chair-white__0872092_pe716742_s5.avif', 'In Stock', '2024-05-21 05:25:08', NULL, 10),
(6062, 2003, 7018, 'Contemporary Dining Chair', 'Modern Living Furnishings', 159.99, 179.99, 'The Contemporary Dining Chair offers a stylish seating option with its modern silhouette and neutral color palette, blending seamlessly into any dining setting.', 'odger-chair-red__1038449_pe839684_s5.avif', 'odger-chair-red__1038451_pe839685_s5.avif', 'odger-chair-red__1038450_pe839686_s5.avif', 'In Stock', '2024-05-21 05:25:08', NULL, 30),
(6063, 2003, 7019, 'NORDVIKEN Dining Table', 'Harmony Home Furnishings', 699.99, 799.99, 'The NORDVIKEN Dining Table features a timeless design with sturdy construction, perfect for everyday meals and gatherings.', 'melltorp-table-white__0737266_pe740964_s5.avif', 'melltorp-table-white__1352026_pe952104_s5.webp', 'melltorp-table-white__1081099_ph173814_s5.webp', 'In Stock', '2024-05-21 05:29:13', NULL, 20),
(6064, 2003, 7019, 'EKEDALEN Dining Table', 'Urban Living Designs', 799.99, 899.99, 'The EKEDALEN Dining Table offers a modern and versatile design, ideal for small or large dining spaces.', 'skogsta-dining-table-acacia__0546603_pe656255_s5.avif', 'skogsta-dining-table-acacia__1342684_pe949087_s5.avif', 'skogsta-dining-table-acacia__1015064_ph176248_s5.avif', 'In Stock', '2024-05-21 05:29:13', NULL, 20),
(6065, 2003, 7019, 'BJURSTA Dining Table', 'Timeless Furnishings Co.', 649.99, 749.99, 'The BJURSTA Dining Table combines functionality and style with its extendable design, providing flexibility for any occasion.', 'voxloev-dining-table-light-bamboo__0997396_pe822660_s5.avif', 'voxloev-dining-table-light-bamboo__0997394_ph176797_s5.avif', 'voxloev-dining-table-light-bamboo__0997060_ph176798_s5.avif', 'In Stock', '2024-05-21 05:29:13', NULL, 12),
(6066, 2003, 7019, 'LERHAMN Dining Table', 'Country Cottage Creations', 749.99, 849.99, 'The LERHAMN Dining Table features a classic and rustic design, bringing a cozy feel to your dining area.', 'norden-gateleg-table-birch__66396_pe179294_s5.avif', 'norden-gateleg-table-birch__1050999_pe844951_s5.avif', 'norden-gateleg-table-birch__0871958_pe593532_s5.avif', 'In Stock', '2024-05-21 05:29:13', NULL, 30),
(6067, 2003, 7020, 'HAVSTA Base Cabinet with Doors and Drawer', 'Harmony Home Furnishings', 599.99, 699.99, 'The HAVSTA Base Cabinet with Doors and Drawer offers convenient storage solutions for your kitchen essentials, with a modern and minimalist design.', 'knoxhult-base-cabinet-with-doors-and-drawer-white__0630745_pe694873_s5.avif', 'knoxhult-base-cabinet-with-doors-and-drawer-white__0871266_pe615157_s5.avif', 'knoxhult-base-cabinet-with-doors-and-drawer-white__0871924_pe617193_s5.avif', 'In Stock', '2024-05-21 05:33:09', NULL, 23),
(6068, 2003, 7020, 'SEKTION Base Cabinet with Doors', 'Urban Living Designs', 499.99, 599.99, 'The SEKTION Base Cabinet with Doors provides ample storage space while maintaining a sleek and contemporary look, perfect for any kitchen style.', 'knoxhult-base-cabinet-with-doors-white__0630733_pe694861_s5.avif', 'knoxhult-base-cabinet-with-doors-white__0871269_pe617211_s5.avif', 'knoxhult-base-cabinet-with-doors-white__0871266_pe615157_s5.avif', 'In Stock', '2024-05-21 05:33:09', NULL, 44),
(6069, 2003, 7020, 'RÅVAROR Corner Kitchen', 'Timeless Furnishings Co.', 1999.99, 2299.99, 'The RÅVAROR Corner Kitchen optimizes space with its smart design, offering functionality and style in one compact unit, ideal for modern kitchens.', 'knoxhult-corner-kitchen-white__0957638_pe805074_s5.avif', 'knoxhult-corner-kitchen-white__0871439_pe615168_s5.avif', 'knoxhult-corner-kitchen-white__0871443_pe615176_s5.avif', 'In Stock', '2024-05-21 05:33:09', NULL, 33),
(6070, 2003, 7021, 'INGOLF Bar Stool', 'Nordic Comfort Designs', 79.99, 89.99, 'The INGOLF Bar Stool combines classic style with comfort, making it perfect for your kitchen counter or bar area.', 'marius-stool-black__0727386_pe735638_s5.avif', 'marius-stool-black__1294232_ph170163_s5.avif', 'marius-stool-black__1053323_pe846903_s5.avif', 'In Stock', '2024-05-21 05:35:20', NULL, 45),
(6071, 2003, 7021, 'FROSTA Stool', 'Modern Living Solutions', 29.99, 39.99, 'The FROSTA Stool features a simple yet versatile design, suitable for use as a seat or a side table in any room of your home.', 'kyrre-stool-birch__0714153_pe729952_s5.avif', 'kyrre-stool-birch__1076529_ph180023_s5.webp', 'kyrre-stool-birch__1076257_ph180018_s5.avif', 'In Stock', '2024-05-21 05:35:20', NULL, 22),
(6072, 2004, 7022, 'MALM Study Table with 1 Drawer', 'Nordic Comfort Designs', 99.99, 109.99, 'The MALM Study Table with 1 Drawer offers a sleek and minimalist design, perfect for any modern workspace. Its single drawer provides convenient storage for essentials.', 'micke-desk-white__0736022_pe740349_s5.avif', 'micke-desk-white__0921905_pe787996_s5.webp', 'micke-desk-white__0849937_pe565227_s5.avif', 'In Stock', '2024-05-21 05:38:20', NULL, 10),
(6073, 2004, 7022, 'HEMNES Study Table with 3 Drawers', 'Modern Living Solutions', 149.99, 169.99, 'The HEMNES Study Table with 3 Drawers features a classic yet functional design, ideal for organizing your workspace. With three spacious drawers, it offers ample storage for books, stationery, and more.', 'micke-desk-white__0736018_pe740345_s5.avif', 'micke-desk-white__0773258_ph161164_s5.avif', 'micke-desk-white__0851508_pe565256_s5.avif', 'In Stock', '2024-05-21 05:38:20', NULL, 30),
(6074, 2004, 7022, 'LISABO Study Table with 5 Drawers', 'Scandinavian Workspace Essentials', 199.99, 219.99, 'The LISABO Study Table with 5 Drawers combines Scandinavian style with practicality. With five integrated drawers, it provides plenty of storage space while maintaining a clean and contemporary look.', 'lagkapten-alex-desk-white__0977483_pe813612_s5.avif', 'lagkapten-alex-desk-white__0977795_pe813778_s5.avif', 'lagkapten-alex-desk-white__1028366_pe835304_s5.avif', 'In Stock', '2024-05-21 05:38:20', NULL, 23),
(6075, 2004, 7023, 'MARKUS Swivel Chair', 'Nordic Comfort Designs', 129.99, 139.99, 'The MARKUS Swivel Chair combines ergonomic design with modern style. Its adjustable height and tilt function provide personalized comfort for long hours of work or study.', 'loberget-malskaer-swivel-chair-white__1078458_pe857202_s5.avif', 'loberget-malskaer-swivel-chair-white__1078478_pe857214_s5.avif', 'loberget-malskaer-swivel-chair-white__1078459_pe857203_s5.avif', 'In Stock', '2024-05-21 05:40:43', NULL, 34),
(6076, 2004, 7023, 'ALEFJÄLL Office Chair with Armrests', 'Scandinavian Workspace Essentials', 179.99, 199.99, 'The ALEFJÄLL Office Chair with Armrests offers both comfort and support for your workspace. With padded armrests and adjustable height, it is designed to enhance your productivity and well-being.', 'flintan-office-chair-with-armrests-black__1007241_pe825960_s5.avif', 'flintan-office-chair-with-armrests-black__1121378_pe874225_s5.avif', 'flintan-office-chair-with-armrests-black__1184182_pe897772_s5.avif', 'In Stock', '2024-05-21 05:40:43', NULL, 27),
(6077, 2004, 7024, 'BILLY Book Shelf', 'Scandinavian Workspace Essentials', 79.99, 89.99, 'The BILLY Book Shelf is a timeless classic for any home office or living space. With adjustable shelves and a sturdy construction, it provides ample storage for your books and decor.', 'gersby-bookcase-white__0251910_pe390723_s5.avif', 'gersby-bookcase-white__0394576_pe561399_s5.avif', 'gersby-bookcase-white__1056934_pe848658_s5.avif', 'In Stock', '2024-05-21 05:43:02', NULL, 28),
(6078, 2004, 7024, 'HEMNES Book Shelf with Doors', 'Nordic Comfort Designs', 149.99, 169.99, 'The HEMNES Book Shelf with Doors combines storage and style in one elegant piece. Its glass doors and adjustable shelves allow you to display your favorite books while keeping them dust-free.', 'billy-oxberg-bookcase-with-panel-glass-doors-white__0667793_pe714087_s5.avif', 'billy-oxberg-bookcase-with-panel-glass-doors-white__1237478_pe917986_s5.avif', 'billy-oxberg-bookcase-with-panel-glass-doors-white__1026878_pe834596_s5.avif', 'In Stock', '2024-05-21 05:43:02', NULL, 57),
(6079, 2004, 7025, 'MALM Cabinet with Doors', 'Nordic Workspace Solutions', 129.99, 149.99, 'The MALM Cabinet with Doors offers versatile storage solutions for any room. With adjustable shelves and a sleek design, it complements any modern decor.', 'galant-cabinet-with-doors-white__0612970_pe686104_s5.avif', 'galant-cabinet-with-doors-white__0856455_pe709361_s5.avif', 'galant-cabinet-with-doors-white__1160579_pe888975_s5.avif', 'In Stock', '2024-05-21 05:54:53', NULL, 23),
(6080, 2004, 7025, 'BRIMNES Monitor Stand with Drawer', 'Scandinavian Home Office Innovations', 59.99, 69.99, 'The BRIMNES Monitor Stand with Drawer is a practical addition to your home office setup. Its integrated drawer provides convenient storage for office supplies, while its minimalist design adds style to your workspace.', 'elloven-monitor-stand-with-drawer-anthracite__0955983_pe804427_s5.avif', 'elloven-monitor-stand-with-drawer-anthracite__0964054_pe808854_s5.avif', 'elloven-monitor-stand-with-drawer-anthracite__0964059_pe808857_s5.avif', 'In Stock', '2024-05-21 05:54:53', NULL, 27),
(6081, 2004, 7025, 'ALEX Drawer Unit with 2 Drawers on Castors', 'Nordic Storage Solutions', 89.99, 99.99, 'The ALEX Drawer Unit with 2 Drawers on Castors offers portable storage with a modern twist. Its smooth-running castors and sleek design make it ideal for organizing your office essentials.', 'trotten-drawer-unit-w-2-drawers-on-castors-white__1009420_pe827590_s5.avif', 'trotten-drawer-unit-w-2-drawers-on-castors-white__1025919_pe834228_s5.avif', 'trotten-drawer-unit-w-2-drawers-on-castors-white__1025921_pe834229_s5.avif', 'In Stock', '2024-05-21 05:54:53', NULL, 20),
(6082, 2005, 7026, 'HEMNES Console Table', 'Scandinavian Furniture Co', 159.99, 179.99, 'The HEMNES Console Table adds elegance and functionality to your living space. With its timeless design and sturdy construction, it is perfect for displaying decor or storing essentials.', 'idanaes-console-table-dark-brown-stained__1161076_pe889284_s5.avif', 'idanaes-console-table-dark-brown-stained__1169538_pe892488_s5.avif', 'idanaes-console-table-dark-brown-stained__1161075_pe889283_s5.avif', 'In Stock', '2024-05-21 05:56:05', NULL, 39),
(6083, 2005, 7027, 'NORDLI Storage Bench', 'Scandinavian Furniture Co', 199.99, 229.99, 'The NORDLI Storage Bench offers a stylish solution for extra seating and storage. With a cushioned top and spacious compartments, it is perfect for any entryway or bedroom.', 'kornsjoe-storage-bench-black__0806412_pe769941_s5.avif', 'kornsjoe-storage-bench-black__0862401_pe781261_s5.avif', 'kornsjoe-storage-bench-black__0884451_pe781859_s5.avif', 'In Stock', '2024-05-21 05:58:42', NULL, 19),
(6084, 2005, 7027, 'TARNBY Hallway Bench', 'Great Furniture Co', 149.99, 169.99, 'The TÄRNBY Hallway Bench combines seating with practicality. Featuring a comfortable seat and a lower shelf for shoes, it is an ideal addition to any hallway.', 'klimpfjaell-bench-grey-brown__0976442_pe813227_s5.avif', 'klimpfjaell-bench-grey-brown__1263075_ph184732_s5.avif', 'klimpfjaell-bench-grey-brown__0992059_pe819782_s5.avif', 'In Stock', '2024-05-21 05:58:42', NULL, 38),
(6085, 2005, 7028, 'BILLY Shoe Rack', 'Shoes Furniture Co.', 79.99, 99.99, 'The BILLY Shoe Rack provides ample storage space for your footwear. Its sleek design fits perfectly in any entryway or closet.', 'trones-shoe-cabinet-storage-white__0710711_pe727734_s5.avif', 'trones-shoe-cabinet-storage-white__0168443_pe316949_s5.avif', 'trones-shoe-cabinet-storage-white__0391784_pe560005_s5.avif', 'In Stock', '2024-05-21 06:00:28', NULL, 21),
(6086, 2005, 7028, 'HEMNES Shoe Organizer', 'Shoes Furniture Co.', 89.99, 119.99, '		The HEMNES Shoe Organizer combines style with functionality, offering multiple shelves for shoe storage. Perfect for keeping your entryway neat and tidy.		', 'kleppstad-shoe-cabinet-storage-white__1005444_pe825460_s5.avif', 'kleppstad-shoe-cabinet-storage-white__1005446_pe825462_s5.avif', 'kleppstad-shoe-cabinet-storage-white__1005445_pe825463_s5.avif', 'Out of Stock', '2024-05-21 06:00:28', NULL, 24);

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `categoryid` int(11) DEFAULT NULL,
  `subcategory` varchar(255) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) DEFAULT NULL,
  `SubStatus` varchar(55) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`id`, `categoryid`, `subcategory`, `creationDate`, `updationDate`, `SubStatus`) VALUES
(7001, 2001, 'Bed Frames', '2024-05-21 04:27:17', NULL, 'Active'),
(7002, 2001, 'Bedside Tables', '2024-05-21 04:27:25', NULL, 'Active'),
(7003, 2001, 'Dressing Tables and Stools', '2024-05-21 04:27:41', NULL, 'Active'),
(7004, 2001, 'Mirrors and Coat Stands', '2024-05-21 04:27:50', NULL, 'Active'),
(7005, 2001, 'Bedroom Cabinets', '2024-05-21 04:28:01', NULL, 'Active'),
(7006, 2001, 'Wardrobes', '2024-05-21 04:28:06', NULL, 'Active'),
(7007, 2001, 'Mattress', '2024-05-21 04:28:12', NULL, 'Active'),
(7008, 2001, 'Bedsheet', '2024-05-21 04:28:18', NULL, 'Active'),
(7009, 2002, 'Armchairs', '2024-05-21 04:28:47', NULL, 'Active'),
(7010, 2002, 'Sofas', '2024-05-21 04:28:52', NULL, 'Active'),
(7011, 2002, 'Coffee Tables', '2024-05-21 04:28:58', NULL, 'Active'),
(7012, 2002, 'TV Cabinets', '2024-05-21 04:29:04', NULL, 'Active'),
(7013, 2002, 'Side Tables', '2024-05-21 04:29:10', NULL, 'Active'),
(7014, 2002, 'Shelf', '2024-05-21 04:29:16', NULL, 'Active'),
(7015, 2002, 'Living Room Cabinets', '2024-05-21 04:29:23', NULL, 'Active'),
(7016, 2002, 'Kids Chair', '2024-05-21 04:29:33', NULL, 'Active'),
(7017, 2003, 'Dining Set', '2024-05-21 04:30:06', NULL, 'Active'),
(7018, 2003, 'Dining Chairs', '2024-05-21 04:30:12', NULL, 'Active'),
(7019, 2003, 'Dining Tables', '2024-05-21 04:30:19', NULL, 'Active'),
(7020, 2003, 'Kitchen Cabinets', '2024-05-21 04:30:26', NULL, 'Active'),
(7021, 2003, 'Stools and Benches', '2024-05-21 04:30:40', NULL, 'Active'),
(7022, 2004, 'Study Tables', '2024-05-21 04:31:11', NULL, 'Active'),
(7023, 2004, 'Study Chairs', '2024-05-21 04:31:19', NULL, 'Active'),
(7024, 2004, 'Book Racks', '2024-05-21 04:31:25', NULL, 'Active'),
(7025, 2004, 'Office Storage', '2024-05-21 04:31:30', NULL, 'Active'),
(7026, 2005, 'Console Tables', '2024-05-21 04:34:07', NULL, 'Active'),
(7027, 2005, 'Hallway Benches', '2024-05-21 04:34:14', NULL, 'Active'),
(7028, 2005, 'Shoe Racks', '2024-05-21 04:34:21', NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT 0.00,
  `transaction_ref` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `order_id`, `user_id`, `action`, `amount`, `transaction_ref`, `transaction_date`) VALUES
(12001, NULL, 9001, 'Reload', 500.00, NULL, '2024-06-17 11:17:30'),
(12002, NULL, 9001, 'Withdraw', 100.00, NULL, '2024-06-17 11:17:37'),
(12003, 3003, 9001, 'Pay', 279.99, NULL, '2024-06-17 11:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `userEmail` varchar(255) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT current_timestamp(),
  `logout` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `userEmail`, `loginTime`, `logout`, `status`) VALUES
(8001, '123@gmail.com', '2024-06-17 11:14:06', '17-06-2024 07:20:43 PM', 1),
(8002, '123@gmail.com', '2024-06-17 11:22:38', '17-06-2024 07:25:29 PM', 1),
(8003, '1211207735@student.mmu.edu.my', '2024-06-17 11:27:16', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contactno` bigint(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `shippingAddress` longtext DEFAULT NULL,
  `shippingState` varchar(255) DEFAULT NULL,
  `shippingCity` varchar(255) DEFAULT NULL,
  `shippingPincode` int(11) DEFAULT NULL,
  `billingAddress` longtext DEFAULT NULL,
  `billingState` varchar(255) DEFAULT NULL,
  `billingCity` varchar(255) DEFAULT NULL,
  `billingPincode` int(11) DEFAULT NULL,
  `regDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `shippingReceiver` varchar(255) DEFAULT NULL,
  `shippingPhone` varchar(20) DEFAULT NULL,
  `billingReceiver` varchar(255) DEFAULT NULL,
  `billingPhone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `balance` float(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contactno`, `password`, `shippingAddress`, `shippingState`, `shippingCity`, `shippingPincode`, `billingAddress`, `billingState`, `billingCity`, `billingPincode`, `regDate`, `updationDate`, `reset_token_hash`, `reset_token_expires_at`, `shippingReceiver`, `shippingPhone`, `billingReceiver`, `billingPhone`, `birthday`, `balance`) VALUES
(9001, 'Chwan Kai', '123@gmail.com', 60167401234, '123', '3, Jalan Sarawak 2', 'Sarawak', 'Bintulu', 67059, '3, Jalan Sarawak 2', 'Sarawak', 'Bintulu', 67059, '2024-06-17 11:13:10', NULL, NULL, NULL, 'Muthu', '+60167403399', 'Muthu', '+60167403399', '2002-01-11', 120.01),
(9002, 'CK Test account', '1211207735@student.mmu.edu.my', 601111111111, '123', 'Jalan Maluri, Taman Conot', 'Selangor', 'Kajang', 28304, 'Jalan Maluri, Taman Conot', 'Selangor', 'Kajang', 28304, '2024-06-17 11:13:57', NULL, NULL, NULL, 'CK Test account', '+601111111111', 'CK Test account', '+601111111111', '2002-07-11', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `postingDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `userId`, `productId`, `postingDate`) VALUES
(10001, 9001, 6078, '2024-06-17 11:14:20'),
(10002, 9001, 6068, '2024-06-17 11:14:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD KEY `orderId` (`orderId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_orders_payment_reference` (`payment_reference`);

--
-- Indexes for table `ordertrackhistory`
--
ALTER TABLE `ordertrackhistory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productreviews`
--
ALTER TABLE `productreviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_transaction_ref` (`transaction_ref`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2009;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3006;

--
-- AUTO_INCREMENT for table `ordertrackhistory`
--
ALTER TABLE `ordertrackhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4005;

--
-- AUTO_INCREMENT for table `productreviews`
--
ALTER TABLE `productreviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5003;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6097;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7034;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12004;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8004;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9003;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10003;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
