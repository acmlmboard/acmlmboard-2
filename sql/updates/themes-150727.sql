-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2015 at 03:57 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ab2`
--

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `basename` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filehash` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `name`, `description`, `disabled`, `basename`, `filename`, `filehash`) VALUES
(1, 'Standard', NULL, 0, '0', '0.css', 'f4565e9c2c2eb8fe8bc4cd741609dfc8'),
(2, 'Alternate', NULL, 0, '1', '1.css', '4c7e01ed3ba36ef4051d6f2dbbfe1f55'),
(3, 'Acmlm''s Board (Acmlm, Mega-Mario, Emuz)', NULL, 0, 'abI', 'abI.css', '6acc8d217acb8e954fae9fd9fd6524f9'),
(4, 'Acmlm''s Board Daily Cycle (Acmlm, Boom.dk, Mega-Mario, Emuz)', NULL, 0, 'abIdailycycle', 'abIdailycycle.php', 'cd6b24650c85f32d9d708b41e405d3e3'),
(5, 'ABXD (Nina)', NULL, 0, 'abxd', 'abxd.css', 'b129fbcd2023bd00fd1723969eb84509'),
(6, 'Acid (Ice Man)', NULL, 0, 'acid', 'acid.css', '3ebd820262ee48dd211fd4499ba8a37a'),
(7, 'AE Sucks (Acmlmboard 1.x Classic)', NULL, 0, 'aesucks', 'aesucks.css', 'e404f546c50050d1a352ed6b81ad48a1'),
(8, 'Arabian Gold (KP9000)', NULL, 0, 'arabgold', 'arabgold.css', '77f28fbe7ec7019215f3a261db373be4'),
(9, 'Acmlm''s ROM Hack Domain (Acmlm, KP9000)', NULL, 0, 'arhd', 'arhd.css', 'ccc990048e849066c10a37145b9e2d11'),
(10, 'Binary Green (Lunaria)', NULL, 0, 'binary-green', 'binary-green.css', '44858a769e76ccf5eed2dec3c1863e23'),
(11, 'Bloodlust (Acmlmboard 1.x Classic)', NULL, 0, 'bloodlust', 'bloodlust.css', 'f8b6e8ef9e97166f55434f9586f7551d'),
(12, 'Blue Storm (KP9000)', NULL, 0, 'bluestorm', 'bluestorm.css', '6f7a84870c8e18c3a23eac99b6b98255'),
(13, 'Black Matrix (KP9000)', NULL, 0, 'bmatrix', 'bmatrix.css', '8a857e456605205bf1cae06e83109cba'),
(14, 'Nostalgia (KP9000)', NULL, 0, 'board2_generic_green', 'board2_generic_green.css', '98955afc3ee32d0d9b5d37e6e93febc1'),
(15, 'Diet board2 (Tyty)', NULL, 0, 'brightblue', 'brightblue.css', 'f2b9d7fe43d791b0a81e458c226cacc6'),
(16, 'Christmas (Acmlm, KP9000)', NULL, 0, 'christmas', 'christmas.css', 'ab638e5bb26d42d8c2b703d091f5bb80'),
(17, 'Classic (Acmlmboard 1.x Classic)', NULL, 0, 'classic', 'classic.css', 'c91c4d63a5417a94049a9ebc340cd351'),
(18, 'Daily Cycle (Boom.dk)', NULL, 0, 'dailycycle', 'dailycycle.php', '5ed1437e28751a22fba6375e6b81bf79'),
(19, 'Daily Cycle 2.0 (beta&trade;) (blackhole89)', NULL, 0, 'dailycycle2', 'dailycycle2.php', '05a09ec5f9f0ebb0a7097e1f6dc0d186'),
(20, 'Danicess (Acmlmboard 1.x Classic)', NULL, 0, 'dani', 'dani.css', '0553a9d94b19521b88f94eb4736f28ff'),
(21, 'Dig (Acmlmboard 1.x Classic)', NULL, 0, 'dig', 'dig.css', 'eb0ebdec796023e9ecf75f5d0475f666'),
(22, 'Emerald Envy (KP9000)', NULL, 0, 'emeraldenvy', 'emeraldenvy.css', '1c58ed2c7a4bdfd9abb2fce461bd441d'),
(23, 'End of Final Fantasy (Acmlmboard 1.x Classic)', NULL, 0, 'endofff', 'endofff.css', '6684e5cb165fef443e8b0b2b471e80be'),
(24, 'AE Torture Revisited', NULL, 0, 'eyerape', 'eyerape.css', 'd72664be56cc74adf05b443493d0e409'),
(25, 'Final Fantasy 9 (Acmlmboard 1.x Classic)', NULL, 0, 'ff9a', 'ff9a.css', '52639bd672ebb8992b905a59044e97ba'),
(26, 'Fire Wave (KP9000)', NULL, 0, 'firewave', 'firewave.css', '71e523ff446bac80e0a98e5ffd1fb38f'),
(27, 'Fish', NULL, 0, 'fish', 'fish.css', '44246bd0337bb69f529daa6d62c2d846'),
(28, 'Got Wood?', 'A completely original theme by Kawa for Board2, who''d have thunk it?', 0, 'gotwood', 'gotwood.css', '8cdfe2126b7f74029d3780c4a06ce17b'),
(29, 'Grape Vineyard (KP9000)', NULL, 0, 'grapevine', 'grapevine.css', 'a88354231396b1a3c1e11cc979465efb'),
(30, 'Green Night (Mega-Mario)', NULL, 0, 'greennight', 'greennight.css', '3985c118a52d3336a6eede4e16067d9f'),
(31, 'Kafuka (Acmlm, KP9000)', NULL, 0, 'kafuka', 'kafuka.css', '82e88f08389e63f547366a1a62fd267f'),
(32, 'Kafuka Gold (Dirbaio)', NULL, 0, 'kafukagold', 'kafukagold.css', 'ef9b86b23bd0f0fc907ccada3db40335'),
(33, 'Kirby (Acmlmboard 1.x Classic)', NULL, 0, 'kirby', 'kirby.css', 'ac4f2c4e2e949e8a5cfd18477bbb0c01'),
(34, 'LMB Purple (Lunaria)', NULL, 0, 'lmb-purple', 'lmb-purple.css', '4f6bf35a0299b9693b4d417b378fb662'),
(35, 'The Left Mouse Button (Taryn)', NULL, 0, 'lmb', 'lmb.css', '70a27e910460b2f7445ca421477d4059'),
(36, 'LMB 2.0 (Liliana, SquidEmpress)', NULL, 0, 'lmb2', 'lmb2.css', 'f37d7d16387e20effc82010634cf901a'),
(37, 'Mario (Acmlm, KP9000)', NULL, 0, 'mario', 'mario.css', '6b2bd2314c02e1273535ad2ed46b230f'),
(38, 'Megaman (Acmlmboard 1.x Classic)', NULL, 0, 'megaman', 'megaman.css', 'a4e24662ed6ed1334fd38f863b6f9c98'),
(39, 'Metro / "Windows 8-style" (Nicole)', NULL, 0, 'metro', 'metro.css', '3877ac8c181858211fa7bce0631b687e'),
(40, 'Miner''s Lament (KP9000)', NULL, 0, 'minerslament', 'minerslament.css', '9dc090d199f5cb8d0fee460f1fe266bb'),
(41, 'Neon (Acmlmboard 1.x Classic)', NULL, 0, 'neon', 'neon.css', '5e451277e400d4583d43e83c35195562'),
(42, 'Acmlmboard NES (Acmlmboard 1.x Classic)', NULL, 0, 'nes', 'nes.css', '003868832a4bd6b8d8b8a6950cda2a90'),
(43, 'Night (Acmlmboard 1.x Classic)', NULL, 0, 'night', 'night.css', '13605d1e4aa792b6524735615778fb14'),
(44, 'Touch of Purples', 'NotRO? Naw. Your imagination.', 0, 'notpurple', 'notpurple.css', '56d68634b7eacc6b41c76799e4693072'),
(45, 'Old Blue (Acmlmboard 1.x Classic)', NULL, 0, 'oldblue', 'oldblue.css', '98137dcec1047c38ed5652d026da3bbe'),
(46, 'Overgrowth (KP9000)', NULL, 0, 'overgrowth', 'overgrowth.css', 'e4dfe4e82a82445ea006192927023772'),
(47, 'Pinkielicious (Ijah)', NULL, 0, 'pinkielicious', 'pinkielicious.css', '6a8110d03d9a3b4880745cf34e6ce4d1'),
(48, 'Purple (Acmlmboard 1.x Classic)', NULL, 0, 'purple', 'purple.css', '616332ff876fc23d83d2fc2a5e8b7ddf'),
(49, 'Remastered Cycle (Nikolaj)', NULL, 0, 'remastered', 'remastered.php', '6385a0829dbdf8b47b8824a54c1c651a'),
(50, 'Simply Luna (Lunaria)', NULL, 0, 'simplyluna', 'simplyluna.css', 'a0a8ae9941f597f5d694fb4689a3a74f'),
(51, 'Snowy', NULL, 0, 'snow', 'snow.css', '47020188cf174518bc5c982e66446846'),
(52, 'Start of the Decade (roxahris)', NULL, 0, 'sotd', 'sotd.css', '73946e163c70af2ff9b9306a7ecbd242'),
(53, 'Tanzanite (KP9000)', NULL, 0, 'tanzanite', 'tanzanite.css', '4f03d5723715580f70832877416934cf'),
(54, 'Terminale Stracarico (SquidEmpress)', NULL, 0, 'terminale_stracarico', 'terminale_stracarico.css', 'f9b089216dde23b07058a132c2bb12da'),
(55, 'Theme of the Day', 'Rotates through every valid theme. Changes theme daily.', 0, 'totd', 'totd.php', '73436d310de6ee1eca3a3522427f140b'),
(56, 'Theme of the Day (Acmlmboard 1.x mix)', 'Rotates through a specified set of classic themes', 0, 'totdclassic', 'totdclassic.php', 'd3c32bdfd7f90112b5716acb3d879dde'),
(57, 'TwilightRO Classic', NULL, 0, 'troclassic', 'troclassic.css', 'd3b581c2434f328006aa46eb0cf16cfb'),
(58, 'Tropicals (roxahris)', NULL, 0, 'tropicals', 'tropicals.css', 'f768e97363b54d21c5762a2703b85d21'),
(59, 'Windows 95 (Danika)', NULL, 0, 'windows95', 'windows95.css', '18bddc651d4b5eeaacb6e43b60f1226a'),
(60, 'Fragmentation (Cold water variety)', NULL, 0, 'xkeeper-2', 'xkeeper-2.css', '266b931453a2f4d35c9b454691fa4158'),
(61, 'Fragmentation (Laggy cold water variety)', NULL, 0, 'xkeeper-21', 'xkeeper-21.css', '1d13957f797979f9bdb73d09eecc3060'),
(62, 'Fragmentation (Xkeeper)', NULL, 0, 'xkeeper', 'xkeeper.css', '3d7777419de795094a6e0a68b42b64b5'),
(63, 'Fragmentation (Lagtastic)', NULL, 0, 'xkeeper1', 'xkeeper1.css', 'd0fb0903ce254dddf1b08cbf6e84731f'),
(64, 'Minus World (blackhole89)', NULL, 0, 'zero', 'zero.css', '6dd44810251ce1a4b58e2504539922b8');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
