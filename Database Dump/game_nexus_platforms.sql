CREATE DATABASE  IF NOT EXISTS `game_nexus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `game_nexus`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: game_nexus
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platforms` (
  `platform` varchar(100) NOT NULL,
  PRIMARY KEY (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platforms`
--

LOCK TABLES `platforms` WRITE;
/*!40000 ALTER TABLE `platforms` DISABLE KEYS */;
INSERT INTO `platforms` VALUES ('1292 Advanced Programmable Video System'),('3DO Interactive Multiplayer'),('Acorn Archimedes'),('Acorn Electron'),('AirConsole'),('Amazon Fire TV'),('Amiga'),('Amiga CD32'),('Amstrad CPC'),('Amstrad PCW'),('Analogue electronics'),('Android'),('Apple II'),('Apple IIGS'),('Apple Pippin'),('Arcade'),('Arcadia 2001'),('Arduboy'),('Atari 2600'),('Atari 5200'),('Atari 7800'),('Atari 8-bit'),('Atari Jaguar'),('Atari Jaguar CD'),('Atari Lynx'),('Atari ST/STE'),('AY-3-8500'),('AY-3-8603'),('AY-3-8605'),('AY-3-8606'),('AY-3-8607'),('AY-3-8610'),('AY-3-8760'),('Bally Astrocade'),('BBC Microcomputer System'),('BlackBerry OS'),('Blu-ray Player'),('Call-A-Computer time-shared mainframe computer system'),('Casio Loopy'),('CDC Cyber 70'),('ColecoVision'),('Commodore 16'),('Commodore C64/128'),('Commodore CDTV'),('Commodore PET'),('Commodore Plus/4'),('Commodore VIC-20'),('Daydream'),('DEC GT40'),('Digiblast'),('Donner Model 30'),('Dragon 32/64'),('Dreamcast'),('DVD Player'),('EDSAC'),('Elektor TV Games Computer'),('Epoch Cassette Vision'),('Epoch Super Cassette Vision'),('Evercade'),('Fairchild Channel F'),('Family Computer (FAMICOM)'),('Family Computer Disk System'),('Ferranti Nimrod Computer'),('FM Towns'),('FM-7'),('Gamate'),('Game & Watch'),('Game Boy'),('Game Boy Advance'),('Game Boy Color'),('Game.com'),('Gear VR'),('Gizmondo'),('Google Stadia'),('Handheld Electronic LCD'),('HP 2100'),('Hyper Neo Geo 64'),('HyperScan'),('Imlac PDS-1'),('Intellivision'),('Intellivision Amico'),('iOS'),('LaserActive'),('Leapster'),('Leapster Explorer/LeadPad Explorer'),('LeapTV'),('Legacy Computer'),('Linux'),('Mac'),('Mega Duck/Cougar Boy'),('Meta Quest 3'),('Microcomputer'),('Microvision'),('Mobile'),('MSX'),('MSX2'),('N-Gage'),('NEC PC-6000 Series'),('Neo Geo AES'),('Neo Geo CD'),('Neo Geo MVS'),('Neo Geo Pocket'),('Neo Geo Pocket Color'),('NES'),('New Nintendo 3DS'),('Nintendo 3DS'),('Nintendo 64'),('Nintendo 64DD'),('Nintendo DS'),('Nintendo DSi'),('Nintendo GameCube'),('Nintendo PlayStation'),('Nintendo Switch'),('Nuon'),('Oculus Go'),('Oculus Quest'),('Oculus Quest 2'),('Oculus Rift'),('Oculus VR'),('Odyssey'),('OnLive Game System'),('OOParts'),('Ouya'),('Palm OS'),('Panasonic Jungle'),('Panasonic M2'),('PC DOS'),('PC Engine SuperGrafx'),('PC-50X Family'),('PC-8801'),('PC-98'),('PC-FX'),('PDP-1'),('PDP-10'),('PDP-11'),('PDP-7'),('PDP-8'),('Philips CD-i'),('Philips Videopac G7000'),('PLATO'),('Playdate'),('Playdia'),('PlayStation'),('PlayStation 2'),('PlayStation 3'),('PlayStation 4'),('PlayStation 5'),('PlayStation Network'),('PlayStation Portable'),('PlayStation Vita'),('PlayStation VR'),('Playstation VR2'),('Plug & Play'),('PocketStation'),('Pokémon mini'),('R-Zone'),('Satellaview'),('SDS Sigma 7'),('Sega 32X'),('Sega CD'),('Sega CD 32X'),('Sega Game Gear'),('Sega Master System'),('Sega Mega Drive/Genesis'),('Sega Pico'),('Sega Saturn'),('SG-1000'),('Sharp MZ-2200'),('Sharp X1'),('Sharp X68000'),('Sinclair QL'),('Sinclair ZX81'),('SNES'),('Stadia'),('SteamVR'),('Super A\'Can'),('Super Famicom'),('Tapwave Zodiac'),('Tatung Einstein'),('Terebikko / See \'n Say Video Phone'),('Texas Instruments TI-99'),('Thomson MO5'),('Tomy Tutor / Pyuta / Grandstand Tutor'),('TRS-80'),('TRS-80 Color Computer'),('TurboGrafx-16/PC Engine'),('Turbografx-16/PC Engine CD'),('Uzebox'),('V.Smile'),('VC 4000'),('Vectrex'),('Virtual Boy'),('visionOS'),('Visual Memory Unit / Visual Memory System'),('Watara/QuickShot Supervision'),('Web browser'),('Wii'),('Wii U'),('Windows Mixed Reality'),('Windows Mobile'),('Windows PC'),('Windows Phone'),('WonderSwan'),('WonderSwan Color'),('Xbox'),('Xbox 360'),('Xbox One'),('Xbox Series'),('Zeebo'),('ZX Spectrum');
/*!40000 ALTER TABLE `platforms` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-29 19:57:18
