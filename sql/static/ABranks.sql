-- phpMyAdmin SQL Dump
-- version 3.4.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 04, 2012 at 12:41 PM
-- Server version: 5.5.24
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `acmlmboard25`
--

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE IF NOT EXISTS `ranks` (
  `rs` int(10) NOT NULL,
  `p` int(10) NOT NULL DEFAULT '0',
  `str` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`rs`, `p`, `str`) VALUES
(1, 0, 'Non-poster'),
(1, 1, 'Newcomer'),
(1, 20, '<img src=img/ranks/goomba.gif width=16 height=16><br>Goomba'),
(1, 10, '<img src=img/ranks/microgoomba.gif width=8 height=9><br>Micro-Goomba'),
(1, 35, '<img src=img/ranks/redgoomba.gif width=16 height=16><br>Red Goomba'),
(1, 50, '<img src=img/ranks/redparagoomba.gif width=20 height=24><br>Red Paragoomba'),
(1, 65, '<img src=img/ranks/paragoomba.gif width=20 height=24><br>Paragoomba'),
(1, 80, '<img src=img/ranks/shyguy.gif width=16 height=16><br>Shyguy'),
(1, 100, '<img src=img/ranks/koopa.gif width=16 height=27><br>Koopa'),
(1, 120, '<img src=img/ranks/redkoopa.gif width=16 height=27><br>Red Koopa'),
(1, 140, '<img src=img/ranks/paratroopa.gif width=16 height=28><br>Paratroopa'),
(1, 160, '<img src=img/ranks/redparatroopa.gif width=16 height=28><br>Red Paratroopa'),
(1, 180, '<img src=img/ranks/cheepcheep.gif width=16 height=16><br>Cheep-cheep'),
(1, 200, '<img src=img/ranks/redcheepcheep.gif width=16 height=16><br>Red Cheep-cheep'),
(1, 225, '<img src=img/ranks/ninji.gif width=16 height=16><br>Ninji'),
(1, 250, '<img src=img/ranks/flurry.gif width=16 height=16><br>Flurry'),
(1, 275, '<img src=img/ranks/snifit.gif width=16 height=16><br>Snifit'),
(1, 300, '<img src=img/ranks/porcupo.gif width=16 height=16><br>Porcupo'),
(1, 325, '<img src=img/ranks/panser.gif width=16 height=16><br>Panser'),
(1, 350, '<img src=img/ranks/mole.gif width=16 height=16><br>Mole'),
(1, 375, '<img src=img/ranks/beetle.gif width=16 height=16><br>Buzzy Beetle'),
(1, 400, '<img src=img/ranks/nipperplant.gif width=16 height=16><br>Nipper Plant'),
(1, 425, '<img src=img/ranks/bloober.gif width=16 height=16><br>Bloober'),
(1, 450, '<img src=img/ranks/busterbeetle.gif width=16 height=15><br>Buster Beetle'),
(1, 475, '<img src=img/ranks/beezo.gif width=16 height=16><br>Beezo'),
(1, 500, '<img src=img/ranks/bulletbill.gif width=16 height=14><br>Bullet Bill'),
(1, 525, '<img src=img/ranks/rex.gif width=20 height=32><br>Rex'),
(1, 550, '<img src=img/ranks/lakitu.gif width=16 height=24><br>Lakitu'),
(1, 575, '<img src=img/ranks/spiny.gif width=16 height=16><br>Spiny'),
(1, 600, '<img src=img/ranks/bobomb.gif width=16 height=16><br>Bob-Omb'),
(1, 700, '<img src=img/ranks/spike.gif width=32 height=32><br>Spike'),
(1, 675, '<img src=img/ranks/pokey.gif width=18 height=64><br>Pokey'),
(1, 650, '<img src=img/ranks/cobrat.gif width=16 height=32><br>Cobrat'),
(1, 725, '<img src=img/ranks/hedgehog.gif width=16 height=24><br>Melon Bug'),
(1, 750, '<img src=img/ranks/lanternghost.gif width=26 height=19><br>Lantern Ghost'),
(1, 775, '<img src=img/ranks/fuzzy.gif width=32 height=31><br>Fuzzy'),
(1, 800, '<img src=img/ranks/bandit.gif width=23 height=28><br>Bandit'),
(1, 830, '<img src=img/ranks/superkoopa.gif width=23 height=13><br>Super Koopa'),
(1, 860, '<img src=img/ranks/redsuperkoopa.gif width=23 height=13><br>Red Super Koopa'),
(1, 900, '<img src=img/ranks/boo.gif width=16 height=16><br>Boo'),
(1, 925, '<img src=img/ranks/boo2.gif width=16 height=16><br>Boo'),
(1, 950, '<img src=img/ranks/fuzzball.gif width=16 height=16><br>Fuzz Ball'),
(1, 1000, '<img src=img/ranks/boomerangbrother.gif width=60 height=40><br>Boomerang Brother'),
(1, 1050, '<img src=img/ranks/hammerbrother.gif width=60 height=40><br>Hammer Brother'),
(1, 1100, '<img src=img/ranks/firebrother.gif width=60 height=24><br>Fire Brother'),
(1, 1150, '<img src=img/ranks/firesnake.gif width=45 height=36><br>Fire Snake'),
(1, 1200, '<img src=img/ranks/giantgoomba.gif width=24 height=23><br>Giant Goomba'),
(1, 1250, '<img src=img/ranks/giantkoopa.gif width=24 height=31><br>Giant Koopa'),
(1, 1300, '<img src=img/ranks/giantredkoopa.gif width=24 height=31><br>Giant Red Koopa'),
(1, 1350, '<img src=img/ranks/giantparatroopa.gif width=24 height=31><br>Giant Paratroopa'),
(1, 1400, '<img src=img/ranks/giantredparatroopa.gif width=24 height=31><br>Giant Red Paratroopa'),
(1, 1450, '<img src=img/ranks/chuck.gif width=28 height=27><br>Chuck'),
(1, 1500, '<img src=img/ranks/thwomp.gif width=44 height=32><br>Thwomp'),
(1, 1550, '<img src=img/ranks/bigcheepcheep.gif width=24 height=32><br>Boss Bass'),
(1, 1600, '<img src=img/ranks/volcanolotus.gif width=32 height=30><br>Volcano Lotus'),
(1, 1650, '<img src=img/ranks/lavalotus.gif width=24 height=32><br>Lava Lotus'),
(1, 1700, '<img src=img/ranks/ptooie2.gif width=16 height=43><br>Ptooie'),
(1, 1800, '<img src=img/ranks/sledgebrother.gif width=60 height=50><br>Sledge Brother'),
(1, 1900, '<img src=img/ranks/boomboom.gif width=28 height=26><br>Boomboom'),
(1, 2000, '<img src=img/ranks/birdopink.gif width=60 height=36><br>Birdo'),
(1, 2100, '<img src=img/ranks/birdored.gif width=60 height=36><br>Red Birdo'),
(1, 2200, '<img src=img/ranks/birdogreen.gif width=60 height=36><br>Green Birdo'),
(1, 2300, '<img src=img/ranks/iggy.gif width=28><br>Larry Koopa'),
(1, 2400, '<img src=img/ranks/morton.gif width=34><br>Morton Koopa'),
(1, 2500, '<img src=img/ranks/wendy.gif width=28><br>Wendy Koopa'),
(1, 2600, '<img src=img/ranks/larry.gif width=28><br>Iggy Koopa'),
(1, 2700, '<img src=img/ranks/roy.gif width=34><br>Roy Koopa'),
(1, 2800, '<img src=img/ranks/lemmy.gif width=28><br>Lemmy Koopa'),
(1, 2900, '<img src=img/ranks/ludwig.gif width=33><br>Ludwig Von Koopa'),
(1, 3000, '<img src=img/ranks/triclyde.gif width=40 height=48><br>Triclyde'),
(1, 3100, '<img src=img/ranks/kamek.gif width=45 height=34><br>Magikoopa'),
(1, 3200, '<img src=img/ranks/wart.gif width=40 height=47><br>Wart'),
(1, 3300, '<img src=img/ranks/babybowser.gif width=36 height=36><br>Baby Bowser'),
(1, 3400, '<img src=img/ranks/bowser.gif width=52 height=49><br>King Bowser Koopa'),
(1, 3500, '<img src=img/ranks/yoshi.gif width=31 height=33><br>Yoshi'),
(1, 3600, '<img src=img/ranks/yoshiyellow.gif width=31 height=32><br>Yellow Yoshi'),
(1, 3700, '<img src=img/ranks/yoshiblue.gif width=36 height=35><br>Blue Yoshi'),
(1, 3800, '<img src=img/ranks/yoshired.gif width=33 height=36><br>Red Yoshi'),
(1, 3900, '<img src=img/ranks/kingyoshi.gif width=24 height=34><br>King Yoshi'),
(1, 4000, '<img src=img/ranks/babymario.gif width=28 height=24><br>Baby Mario'),
(1, 4100, '<img src=img/ranks/luigismall.gif width=15 height=22><br>Luigi'),
(1, 4200, '<img src=img/ranks/mariosmall.gif width=15 height=20><br>Mario'),
(1, 4300, '<img src=img/ranks/luigibig.gif width=16 height=30><br>Super Luigi'),
(1, 4400, '<img src=img/ranks/mariobig.gif width=16 height=28><br>Super Mario'),
(1, 4500, '<img src=img/ranks/luigifire.gif width=16 height=30><br>Fire Luigi'),
(1, 4600, '<img src=img/ranks/mariofire.gif width=16 height=28><br>Fire Mario'),
(1, 4700, '<img src=img/ranks/luigicape.gif width=26 height=30><br>Cape Luigi'),
(1, 4800, '<img src=img/ranks/mariocape.gif width=26 height=28><br>Cape Mario'),
(1, 4900, '<img src=img/ranks/luigistar.gif width=16 height=30><br>Star Luigi'),
(1, 5000, '<img src=img/ranks/mariostar.gif width=16 height=28><br>Star Mario'),
(1, 625, '<img src=img/ranks/drybones.gif><br>Dry Bones'),
(1, 10000, 'Climbing the ranks again!'),
(2, 0, 'Non-poster'),
(2, 1, 'Newcomer'),
(2, 20, '<img src=img/ranksz/Octorok.gif><br>Octorok'),
(2, 10, '<img src=img/ranksz/MiniOctorok.gif><br>Mini Octorok'),
(2, 35, '<img src=img/ranksz/BlueOctorok.gif><br>Blue Octorok'),
(2, 50, '<img src=img/ranksz/Tektite.gif><br>Tektite'),
(2, 65, '<img src=img/ranksz/RedTektite.gif><br>Red Tektite'),
(2, 80, '<img src=img/ranksz/Rat.gif><br>Rat'),
(2, 100, '<img src=img/ranksz/Rope.gif><br>Rope'),
(2, 120, '<img src=img/ranksz/Keese.gif><br>Keese'),
(2, 140, '<img src=img/ranksz/Bee.gif><br>Bee'),
(2, 160, '<img src=img/ranksz/Octoballoon.gif><br>Octoballoon'),
(2, 180, '<img src=img/ranksz/Leever.gif><br>Leever'),
(2, 200, '<img src=img/ranksz/PurpleLeever.gif><br>Purple Leever'),
(2, 220, '<img src=img/ranksz/SandCrab.gif><br>Sand Crab'),
(2, 240, '<img src=img/ranksz/Bit.gif><br>Bit'),
(2, 260, '<img src=img/ranksz/Bot.gif><br>Bot'),
(2, 300, '<img src=img/ranksz/Cukeman.gif><br>Cukeman'),
(2, 325, '<img src=img/ranksz/Slime.gif><br>Slime'),
(2, 350, '<img src=img/ranksz/Hoarder.gif><br>Hoarder'),
(2, 375, '<img src=img/ranksz/Crow.gif><br>Crow'),
(2, 400, '<img src=img/ranksz/Tendoru.gif><br>Tendoru'),
(2, 425, '<img src=img/ranksz/Deddorokku.gif><br>Deddorokku'),
(2, 450, '<img src=img/ranksz/Geldman.gif><br>Geldman'),
(2, 475, '<img src=img/ranksz/Armos.gif><br>Armos'),
(2, 500, '<img src=img/ranksz/Zora.gif><br>Zora'),
(2, 525, '<img src=img/ranksz/Popo.gif><br>Popo'),
(2, 550, '<img src=img/ranksz/HardhatBeetle.gif><br>Hardhat Beetle'),
(2, 575, '<img src=img/ranksz/Kodondo.gif><br>Kodondo'),
(2, 600, '<img src=img/ranksz/Surarok.gif><br>Surarok'),
(2, 700, '<img src=img/ranksz/Raven.gif><br>Raven'),
(2, 675, '<img src=img/ranksz/Chasupa.gif><br>Chasupa'),
(2, 650, '<img src=img/ranksz/Sukarurope.gif><br>Sukarurope'),
(2, 725, '<img src=img/ranksz/Ropa.gif><br>Ropa'),
(2, 750, '<img src=img/ranksz/Zirro.gif><br>Zirro'),
(2, 775, '<img src=img/ranksz/SnapDragon.gif><br>Snap Dragon'),
(2, 800, '<img src=img/ranksz/LikeLike.gif><br>Like Like'),
(2, 830, '<img src=img/ranksz/Poe.gif><br>Poe'),
(2, 860, '<img src=img/ranksz/Moblin.gif><br>Moblin'),
(2, 900, '<img src=img/ranksz/Helmasaur.gif><br>Helmasaur'),
(2, 925, '<img src=img/ranksz/Onoff.gif><br>Onoff'),
(2, 950, '<img src=img/ranksz/Bubble.gif><br>Bubble'),
(2, 1000, '<img src=img/ranksz/Stalfos.gif><br>Stalfos'),
(2, 1050, '<img src=img/ranksz/RedStalfos.gif><br>Red Stalfos'),
(2, 1100, '<img src=img/ranksz/YellowStalfos.gif><br>Yellow Stalfos'),
(2, 1150, '<img src=img/ranksz/Torosu.gif><br>Torosu'),
(2, 1200, '<img src=img/ranksz/RedTorosu.gif><br>Red Torosu'),
(2, 1250, '<img src=img/ranksz/Darknut.gif><br>Darknut'),
(2, 1300, '<img src=img/ranksz/IronKnuckle.gif><br>Iron Knuckle'),
(2, 1350, '<img src=img/ranksz/Vire.gif><br>Vire'),
(2, 1400, '<img src=img/ranksz/Rocklops.gif><br>Rocklops'),
(2, 1450, '<img src=img/ranksz/Beamos.gif><br>Beamos'),
(2, 1500, '<img src=img/ranksz/Wallmaster.gif><br>Wallmaster'),
(2, 1550, '<img src=img/ranksz/Gibdos.gif><br>Gibdo'),
(2, 1600, '<img src=img/ranksz/Wizzrobe.gif><br>Wizzrobe'),
(2, 1650, '<img src=img/ranksz/Lynel.gif><br>Lynel'),
(2, 1700, '<img src=img/ranksz/BallNChainTrooper.gif><br>Ball and Chain Trooper'),
(2, 1800, '<img src=img/ranksz/HinoxBoss.gif><br>Hinox'),
(2, 1900, '<img src=img/ranksz/MasterStalfosTrimmed.gif><br>Master Stalfos'),
(2, 2000, '<img src=img/ranksz/Aquamentus.gif><br>Aquamentus'),
(2, 2100, '<img src=img/ranksz/Dodongo.gif><br>Dodongo'),
(2, 2200, '<img src=img/ranksz/Gohma.gif><br>Gohma'),
(2, 2300, '<img src=img/ranksz/Gleeok.gif><br>Gleeok'),
(2, 2400, '<img src=img/ranksz/Digdogger.gif><br>Digdogger'),
(2, 2500, '<img src=img/ranksz/Manhandla.gif><br>Manhandla'),
(2, 2600, '<img src=img/ranksz/ArmosKnight.gif><br>Armos Knight'),
(2, 2700, '<img src=img/ranksz/Moldorm.gif><br>Moldorm'),
(2, 2800, '<img src=img/ranksz/Arrghus.gif><br>Arrghus'),
(2, 2900, '<img src=img/ranksz/Mothula.gif><br>Mothula'),
(2, 3000, '<img src=img/ranksz/Blind.gif><br>Blind'),
(2, 3100, '<img src=img/ranksz/Kholdstare.gif><br>Kholdstare'),
(2, 3200, '<img src=img/ranksz/Vitreous.gif><br>Vitreous'),
(2, 3300, '<img src=img/ranksz/DarkLink.gif><br>Dark Link'),
(2, 3400, '<img src=img/ranksz/DeathI.gif><br>DethI'),
(2, 3500, '<img src=img/ranksz/Agahnim.gif><br>Agahnim'),
(2, 3600, '<img src=img/ranksz/Ganon.gif><br>Ganon'),
(2, 3700, '<img src=img/ranksz/Zelda.gif><br>Zelda'),
(2, 3800, '<img src=img/ranksz/Link.gif><br>Link'),
(2, 3900, '<img src=img/ranksz/TheAdventureOfLink.gif><br>The Adventure of Link'),
(2, 4000, '<img src=img/ranksz/Link''sAwakening.gif><br>Link''s Awakening'),
(2, 4100, '<img src=img/ranksz/ALinkToThePast.gif><br>A Link to the Past'),
(2, 4200, '<img src=img/ranksz/FighterLink.gif><br>Fighter Link'),
(2, 4300, '<img src=img/ranksz/BlueMailLink.gif><br>Blue Mail Link'),
(2, 4400, '<img src=img/ranksz/RedMailLink.gif><br>Red Mail Link'),
(2, 4500, '<img src=img/ranksz/RabbitLink.gif><br>Rabbit Link'),
(2, 4600, '<img src=img/ranksz/MagicHammer.gif><br>Magic Hammer'),
(2, 4700, '<img src=img/ranksz/CaneOfByrna.gif><br>Cane of Byrna'),
(2, 4800, '<img src=img/ranksz/HeroOfTime.gif><br>Hero of Time'),
(2, 4900, '<img src=img/ranksz/HeroOfWinds.gif><br>Hero of Winds'),
(2, 5000, '<img src=img/ranksz/HeroOfHyrule.gif><br>Hero of Hyrule'),
(2, 625, '<img src=img/ranksz/Bazu.gif><br>Bazu'),
(2, 10000, 'Climbing the ranks again!'),
(2, 280, '<img src=img/ranksz/BuzzBlob.gif><br>Buzz Blob');

-- --------------------------------------------------------

--
-- Table structure for table `ranksets`
--

CREATE TABLE IF NOT EXISTS `ranksets` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ranksets`
--

INSERT INTO `ranksets` (`id`, `name`) VALUES
(1, 'Mario'),
(0, 'None'),
(2, 'Zelda (by Fyxe)');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
