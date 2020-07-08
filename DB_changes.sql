-- Rev 1.0
ALTER TABLE `guest` CHANGE COLUMN `g_name` `g_first_name` VARCHAR(35) NOT NULL;
ALTER TABLE `guest` ADD `g_last_name` VARCHAR(35) NOT NULL after `g_first_name`;
ALTER TABLE badges MODIFY `first_name` VARCHAR(35) NOT NULL;
ALTER TABLE badges MODIFY `last_name` VARCHAR(35) NOT NULL;
ALTER TABLE badges MODIFY badge_subscription_id int(11) NULL;
ALTER TABLE work_credit_transactions MODIFY badge_number int(6) NOT NULL;

-- Rev 2
ALTER TABLE clubs DROP COLUMN poc;

ALTER TABLE clubs MODIFY status int(1) NOT NULL;
UPDATE clubs SET status=status-1;

-- Rev 3
UPDATE badges set status='approved' where status='active';
ALTER TABLE badges MODIFY yob int(4);

-- Rev 5
insert into membership_type values (70,'15yr','0');
insert into fees_structure values (8,'15yr Badge',70,1750.00,0,'badge_fee');
update fees_structure set membership_id=70 where id =8;

-- Rev 7
ALTER TABLE `work_credits` CHANGE COLUMN `created_role` `created_by` int(6) NOT NULL;
ALTER TABLE `work_credits` MODIFY status int(1) NOT NULL;

-- Rev 8
ALTER TABLE badge_subscriptions MODIFY status VARCHAR(12) NOT NULL;

-- Rev 12
ALTER TABLE work_credits ADD supervisor varchar(255) NOT NULL after authorized_by;
ALTER TABLE work_credits MODIFY authorized_by  varchar(255);

-- Rev 17
ALTER TABLE badges DROP sticker;
UPDATE badges SET status='approved' where status ='active';

-- Rev 18
INSERT INTO user_privileges VALUE (5,'space');
UPDATE user_privileges SET privilege="Member" where id=5;
UPDATE user_privileges SET privilege="View" where id=4;
UPDATE user set privilege=5 where privilege=4;
ALTER TABLE `params` ADD `sell_date` VARCHAR(5) NOT NULL after `id`;
UPDATE params SET sell_date='10-20' WHERE id=1;

-- Rev x.9
DROP TABLE IF EXISTS `badge_to_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_to_club` (
`id` int(11)  UNIQUE  AUTO_INCREMENT,
`badge_number` int(5) NOT NULL,
`club_id` int(5) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

ALTER TABLE badges DROP club_name;

-- Rev x.18

DROP TABLE IF EXISTS `violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `violations` (
`id` int(11)  UNIQUE  AUTO_INCREMENT,
`badge_reporter` int(5) NOT NULL,
`vi_type` int(2) NOT NULL,
`badge_involved` varchar(255) NOT NULL,
`badge_witness` varchar(255) NOT NULL,
`vi_date` datetime NOT NULL,
`vi_sum` varchar(255) NOT NULL,
`vi_rules` varchar(255) NOT NULL,
`vi_report` text NOT NULL,
`vi_action` text NOT NULL,
`hear_date` datetime,
`hear_sum` text,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `rule_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rule_list` (
`id` int(11)  UNIQUE  AUTO_INCREMENT,
`rule_abrev` varchar(6) NOT NULL,
`rule_name` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `rule_list` (rule_abrev,rule_name)
VALUES ('1C3D','No ECI'),('1B2','No Badge'),('1C3A','Handling Firearm During Ceasefire'),
('2C1','Not Hitting Impact Area'),('1C9C','Magazine inserted during Ceasefire');

DROP TABLE migration;
DROP TABLE credit_transfer_queue;

-- Rev 25
ALTER TABLE rule_list ADD vi_type int(2) NOT NULL after rule_abrev;

-- Rev 32
truncate rule_list;

INSERT INTO rule_list (rule_abrev,vi_type,rule_name) VALUES
('IA1',3,'Never allow the gun to point at anything you do not intend to shoot.'),('IA1',4,'Never allow LOADED gun to point at anything you do not intend to shoot.'),('IB1',3,'Arguing with RSO, AGC Officer, or Match Director'),('IB1',4,'Refusing to follow the directions of RSO, AGC Officer, or Match Director'),('IB2',1,'Range badges shall be in the possession of the named badge holder and readily visible at all times while on AGC property.'),('IB2',4,'Range badges may not be loaned or transferred to, or in the possession of, any other person.'),('IB3',4,'Persons prohibited by any Federal, State or local law from owning or possessing firearms are specifically prohibited from entering upon AGC property and are subject to arrest for trespassing.'),('IB4',1,'Guest badges shall be in the possession of the person it was issued to and readily visible at all times while on AGC property.'),('IB4',2,'Adult badge holders sign in their guests on the log provided and be issued a Guest badge.'),('IB4',4,'Guest badges may not be loaned or transferred to, or in the possession of, any other person.'),('IB6',1,'A badge holder with guests may only occupy one firing point and ONLY ONE PERSON in your party may fire at a time.'),('IB8',1,'Guests shall park in the outer parking lot when using the 50 or 100-yard ranges.'),('IB10',4,'Consumption of alcohol is permitted in the Barnes Range House and Memorial Hall ONLY; no alcoholic beverages are permitted on or near any AGC range firing area.'),('IB11',2,'Pets shall be accompanied by their owner, leashed and under control at all times.'),('IB12',4,'AGC reserves the right to remove and permanently ban any member, non-member, guest or student without refund for violent, inappropriate, rude, disorderly, threatening, unsportsmanlike or intoxicated behavior.'),('IB13',2,'Parking is permitted in designated areas as posted.'),('IB14',2,'Driving onto or parking on any of the ranges is prohibited unless prior permission is granted by the RSO or Executive VP.'),('IB15',3,'Instruction or demonstration involving drawing from holster, or aiming, or aiming and dry firing is prohibited in all buildings.'),('IC1a',4,'Do not touch firearms during a Cease Fire!'),('IC1b',3,'A Cease Fire is in effect from when it is called at the end of the day until the range is called HOT the following morning.'),('IC1c',2,'During a Cease Fire, all uncased firearms shall remain pointed downrange or racked in an upright position with actions open, magazines removed and Empty Chamber Indicator (ECI) in place. (C2'),('IC1c',4,'During a Cease Fire, all uncased firearms shall remain unloaded. '),('IC1d',1,'During a Cease Fire, you shall remain behind the White Stripe when not pulling or posting targets.'),('IC2',2,'Shooters shall, if they leave the firing line for any reason, safe their firearms per I.C.3.d., and instruct their guest, if any, to remain behind the White Stripe.'),('IC3',4,'Firearms containing ammunition in any manner shall NOT be brought onto AGC property.'),('IC5',3,'All uncased firearms shall be carried muzzle up while being carried from place to place.'),('IC8a',2,'LOADED if an Empty Chamber Indicator (ECI) is not in place.'),('IC8b',2,'LOADED if actions, cylinders or loading gates are closed.'),('IC8c',2,'LOADED if empty cases are in the chamber/cylinder/fixed magazine, or if a removable empty magazine is inserted.'),('IC8c',4,'LOADED if cartridges are in the chamber/cylinder/fixed magazine, or if a removable magazine with carridges is inserted.'),('IC8d',3,'LOADED if Black Powder Firearms containing: propellant, projectile or cap; powder in the pan of a flintlock.'),('IC9',2,'Uncased firearms shall NOT be brought onto or taken from the Concrete Pad when a Cease Fire is in effect.'),('IC10',3,'Cased firearms may be brought onto the Concrete Pad and placed on the ground or shooting bench at any time.  You shall NOT open the case or otherwise handle the firearm until the line is called HOT.'),('IC11',3,'Firearms shall be cased or uncased on the shooting bench/table and remain pointed downrange at all times while on the firing line.'),('IC12',2,'Containers of propellant shall be kept closed when not being used.'),('IC13',2,'Cleaning of firearms on the Concrete Pad is permitted with muzzles pointed downrange or upright.'),('IC14',2,'Cleaning of firearms off the Concrete Pad is permitted only if the firearm action is clearly disabled; firearm disassembled, bolt removed, etc.'),('IC14',4,'Cleaning of firearms with ammunition present.'),('IC15',1,'No one shall fire at any target not in their lane.'),('IC16',4,'No one shall fire at any wildlife.'),('IC17',4,'No one shall fire at any permanent structure or fixture or engage in willful destruction of property.'),('IC18',2,'Semi-automatic strings may be fired on any range at a rate that allows the aiming and control of each shot.  All shots fired must strike within the designated Impact Area for the shooter’s position.'),('IC19',2,'The Firing Line on the 50, 100 and 200-yard ranges is the forward edge of the Concrete Pad.'),('IC20',2,'Shooters shall position themselves so the muzzle of their firearm is at or beyond the forward edge of the Concrete Pad.'),('IC20',3,'Under NO CIRCUMSTANCES will a firearm be discharged if the muzzle is behind any person or behind a roof support pole on the Concrete Pad.'),('IC21',3,'No one shall go forward of the Firing Line (See I.C.21) while the line is hot.'),('IC22',2,'If a firearm fails to fire, the muzzle shall remain pointed at the Impact Area for a minimum of 30 seconds before remedial action is taken.'),('IC23',3,'Firearms, ammunition and ammunition components shall not be stored on AGC property.'),('IC24',3,'Tracer, incendiary and explosive ammunition is prohibited.'),('IC25',2,'Targets and target frames must not be capable of deflecting a projectile in an unsafe direction.'),('IC26',3,'Fully automatic fire is only permitted as detailed in Chapter XII of the Policy & Procedures manual.'),('IC27',3,'Holstered firearms may be worn only under applicable Maryland law, within the constraints and conditions of your carry permit.'),('IC28',3,'Drawing from holsters is only permitted as detailed in Chapter  XXI of the Policy & Procedures Manual.'),('IC29',1,'Shooters shall clean up their area and police their brass and shotshell hulls when finished shooting and firearms are not being handled.'),('IIA1',2,'Rounds must hit impact berm'),('IIA2',1,'On the 50, 100 and 200-yard ranges, other than paper targets may be used provided that all fired rounds easily pass through them and strike the Impact Area. '),('IIA3',1,'Pictures, caricatures or illustrations depicting real people are prohibited.'),('IIA4',3,'Exploding targets are prohibited.'),('IIA5',2,'Glass targets or those containing glass are prohibited.'),('IIA6',1,'Targets shall NOT be placed on the Impact Areas.'),('IIA7',3,'Targets shall NOT be placed on the Protective Berms.'),('IIA8',1,'Targets shall be placed in the location that matches the shooter’s lane number.'),('IIB2',2,'You must display your named yellow badge with certification sticker in addition to your range badge when shooting at steel targets.'),('IIB3',2,'Steel targets and their mounts shall be submitted for inspection and approval by the Executive VP or his/her designee before initial use and are subject to inspection at any time.'),('IIB3b',2,'Pitted, cratered, holed, bent, warped or otherwise damaged targets are prohibited.'),('IIB4a',2,'Prohibited ammunition: Rifle rounds exceeding 3150 fps muzzle velocity.'),('IIB4b',2,'Prohibited ammunition: Pistol rounds exceeding 1500 fps muzzle velocity.'),('IIB4c',2,'Prohibited ammunition: Any round with a muzzle velocity less than 750 fps.'),('IIB4d',2,'Prohibited ammunition: Any round labeled “Magnum”.'),('IIB4e',2,'Prohibited ammunition: Armor piercing, steel core or ‘penetrator’.'),('IIB4f',2,'Prohibited ammunition: 50 BMG and all long-range tactical rounds.'),('IIB4g',2,'Prohibited ammunition: Shotgun slugs.'),('IIB4h',2,'Prohibited ammunition: 5.7 X 28 ammunition'),('III1',2,'Smoking is prohibited within 15 feet of black powder or black powder substitutes.'),('III2',1,'Prior to loading, shooters using muzzle loading rifles or pistols shall fire caps on all nipples of percussion firearms, or a pan full of powder in a flintlock, while pointing the firearm downrange.'),('III3',3,'Muzzle loading firearms using granulated propellant shall have the propellant poured into the muzzle from a powder measure.'),('III4',2,'Containers of propellant shall be kept closed when not being used.'),('III5',2,'Shooters using muzzle loading rifles shall place their rifle muzzle up in a v-notch in the loading bench or some other device during a Cease Fire or during loading.'),('III6',2,'Percussion and flintlock firearms shall be positioned with the muzzle forward of the Firing Line and pointed downrange when a percussion cap is affixed or when the pan is charged.'),('III7',2,'Muzzle loading handguns shall be placed muzzle up in a loading stand or similar device during a Cease Fire.'),('IVA1',2,'This range is designated for the shooting of pistol-caliber handguns with barrels 10” or less in length.'),('IVA1a',2,'Handgun cartridges with ballistics between .22 rimfire and .500 S&W are permitted.'),('IVA2',2,'Rifle-caliber handguns are prohibited.'),('IVA3',2,'Shot shells shall NOT be fired on this range.'),('IVA4',1,'Firing from a position other than standing, or sitting on a stool, is prohibited.'),('IVA5',2,'When AGC-owned frames are used, only one target with a single, centered aiming point is permitted.'),('IVB2',1,'Positions to the left of the orange roof support pole (at lane 57) are normally closed to use. '),('IVB3',1,'The 10 fixed benches on the far right of the Barnes range are for Benchrest Position shooting only.'),('IVB4',1,'In the Benchrest area, everyone must be behind the Red Zone while the line is hot.'),('IVB5',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.'),('IVC2',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.'),('IVC3',3,'An orange flag shall be displayed forward of the firing line when anyone is downrange.'),('IVC4',3,'The target carriages shall ONLY be used for firing properly sighted-in rifles at paper targets.'),('IVC5a',2,'A conventional bullseye target shall be centered in the target frame.'),('IVC5b',2,'Multiple aiming point targets, or any target other than a conventional bullseye target, shall be mounted with the aiming point no closer than 12” from the frame side members and all your shots must strike on the target paper.'),('IVC6',2,'Silhouettes, gongs, and spinners may be used for silhouette or hunting HANDGUN practice ONLY and shall be positioned directly in front of the 50, 100 or 150-meter berms or 200-meter Impact Area.'),('IVC7',2,'Firing a rifle at any target placed anywhere closer than 200 yards is prohibited.'),('IVC8',2,'Portable target frames may be placed behind the 200-yard pits immediately in front of the impact area.'),('IVC9',2,'Portable target frames with PAPER TARGETS may be placed atop the protective berm immediately forward of the pits.'),('IVC10',2,'An AGC-style portable wooden frame with PAPER TARGETS may be placed in the receptacles on the back side of the protective berm bulkhead above the pit roof.'),('IVC11',3,'Firing at objects placed on the protective berms is prohibited.'),('IVC12',3,'People may remain in the pits between ceasefires only during organized shoots/practices under the control of a designated Match Director.'),('IVC13',3,'No personnel are permitted in the 200-yard target pits when shooting steel targets.'),('IVC14',2,'Firearms shall NOT be left unattended.'),('IVC15',2,'Initial sighting in of firearms/scopes/sights is prohibited.'),('IVD2',2,'Shooting forward of the 16-yard line is prohibited.'),('IVD3',3,'Only shotguns firing a maximum powder load of 3 drams equivalent, shot size 7 1/2, 8 or 9, and a maximum muzzle velocity of 1200 fps are permitted.'),('IVD4',4,'Firing slugs is prohibited.'),('IVD5',2,'Shotguns shall remain unloaded with actions open at all times until on station and ready to shoot.'),('IVD5',4,'Shotguns shall actions open at all times until on station and ready to shoot.'),('IVD6',2,'When shooting handicaps, shooters may shoot from a staggered position not to exceed 2 yards.'),('IVD7',2,'Portable traps and other throwing devices may be used when positioned on or behind the 16-yard line.'),('IVD8',2,'No one shall proceed beyond a trap house when any other fields are in use.'),('IVD9',2,'Spent and/or unspent shot shells shall not be picked up until shooters have unloaded and racked their shotguns.'),('IVD10',2,'It is permitted to walk to the trap house if the field is ‘clear’.  Shooters shall unload and rack their shotguns prior to anyone going to the trap house.'),('IVD11',3,'When a person is in a trap house, an orange safety cone shall be placed on top of the trap house.'),('IVD12',2,'All firearms used on the Trap Range shall be fired from the shoulder.'),('IVD13',2,'Folding stocks shall be in the extended position.'),('IVE1',3,'This facility is intended for PATTERNING of shotguns ONLY.  '),('IVE2',4,'Patterning targets shall have a single aiming point centered on the patterning board.'),('IVE3',4,'SLUGS are prohibited,'),('IVE4',4,'LEAD shot sizes larger than #2 are prohibited,'),('IVE5',4,'STEEL shot sizes larger than BBB are prohibited,'),('IVE6',4,'Placing of, or shooting at, objects on top of patterning frame is prohibited,'),('IVF1',2,'Sky drawing is prohibited.'),('IVF2',2,'Only field point or target arrows may be shot at the AGC targets.'),('IVF3',2,'Broad head arrows shall NOT be shot at AGC targets.'),('IVF5',2,'Archers shall designate a common Firing Line.'),('IVG3',2,'Only compressed air, carbon dioxide, and spring-powered guns firing .177 or .22 caliber blunt-nosed lead pellets weighing less than 25 grains may be fired on this range.'),('IVG4',2,'The maximum allowable velocity is 1000 fps for .177 pellets and 800 fps for .22 pellets.'),('IVG5',2,'Only paper targets or AGC-approved metal or metal-clad targets may be used.'),('IVG6',1,'Shooters shall be aligned properly with their pellet traps.');

-- Rev 42
ALTER TABLE badges DROP work_credits;

-- Rev 49
ALTER TABLE violations ADD was_guest TINYINT(1) NOT NULL DEFAULT 0 after vi_action;
ALTER TABLE user_privileges ADD sort int(3) NOT NULL;
UPDATE user_privileges SET sort=id;
update user_privileges set sort=id+1 where id>2;
INSERT INTO user_privileges VALUES (6,'RSO Lead',3);

-- Rev 55
ALTER TABLE post_print_transactions MODIFY  transaction_type varchar(6) NOT NULL;

-- Rev 59
ALTER TABLE user_privileges CHANGE `sort` `priv_sort` int(3) NOT NULL;
ALTER TABLE user_privileges ADD timeout int(3) NOT NULL;
UPDATE user_privileges SET timeout=60 where id=1;
UPDATE user_privileges SET timeout=30 where id=2;
UPDATE user_privileges SET timeout=15;
UPDATE user_privileges SET timeout=3 where id=5;

-- Rev 61
UPDATE badges set address = CONCAT(address,", ",address_op) where address_op<>'';
ALTER TABLE badges DROP COLUMN address_op;
ALTER TABLE badges DROP COLUMN badge_subscription_id;
ALTER TABLE badges DROP COLUMN badge_type;
UPDATE badge_subscriptions SET status='active' WHERE status='approved';

-- Rev 65
UPDATE rule_list set rule_abrev='IC3a' where id=19;
UPDATE rule_list set rule_abrev='IC3b' where id=20;
UPDATE rule_list set rule_abrev='IC3d' where id=21;
UPDATE rule_list set rule_abrev='IC3d' where id=22;
UPDATE rule_list set rule_abrev='IC3e' where id=23;
UPDATE rule_list set rule_abrev='IC4' where id=24;
UPDATE rule_list set rule_abrev='IC5' where id=25;
UPDATE rule_list set rule_abrev='IC7' where id=26;
UPDATE rule_list set rule_abrev='IC10a' where id=27;
UPDATE rule_list set rule_abrev='IC10b' where id=28;
UPDATE rule_list set rule_abrev='IC10c' where id=29 OR id=30;
UPDATE rule_list set rule_abrev='IC10d' where id=31;
UPDATE rule_list set rule_abrev='IC11' where id=32;
UPDATE rule_list set rule_abrev='IC12' where id=33;
UPDATE rule_list set rule_abrev='IC13' where id=34;
UPDATE rule_list set rule_abrev='IC14' where id=35;
UPDATE rule_list set rule_abrev='IC15' where id=36;
UPDATE rule_list set rule_abrev='IC16' where id=37 or id=38;
UPDATE rule_list set rule_abrev='IC17' where id=39;
UPDATE rule_list set rule_abrev='IC18' where id=40;
UPDATE rule_list set rule_abrev='IC19' where id=41;
UPDATE rule_list set rule_abrev='IC20' where id=42;
UPDATE rule_list set rule_abrev='IC21' where id=43;
UPDATE rule_list set rule_abrev='IC22' where id=44 or id=45;
UPDATE rule_list set rule_abrev='IC23' where id=46;
UPDATE rule_list set rule_abrev='IC24' where id=47;
UPDATE rule_list set rule_abrev='IC25' where id=48;
UPDATE rule_list set rule_abrev='IC26' where id=49;
UPDATE rule_list set rule_abrev='IC27' where id=50;
UPDATE rule_list set rule_abrev='IC28' where id=51;
UPDATE rule_list set rule_abrev='IC29' where id=52;
UPDATE rule_list set rule_abrev='IC30' where id=53;
UPDATE rule_list set rule_abrev='IC31' where id=54;

-- Rev 68
ALTER TABLE badge_subscriptions ADD cc_x_id varchar(20);
ALTER TABLE params drop sticker_prefix;
ALTER TABLE params drop badge_prefix;
ALTER TABLE params add qb_realmId varchar(20);
ALTER TABLE params add qb_oauth_cust_key varchar(30);
ALTER TABLE params add qb_oauth_cust_sec varchar(40);
ALTER TABLE params add qb_token_date date;
ALTER TABLE params add qb_token varchar(255);

-- Rev 71
ALTER TABLE badges ADD email_vrfy bool default 0 after email;
UPDATE badges set email_vrfy=0;
ALTER TABLE badges drop email_op;

-- Rev 74
DROP TABLE IF EXISTS `cc_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cc_receipts` (
`id` varchar(15)  UNIQUE  ,
`badge_number` int(6) NOT NULL,
`status` varchar(15) NOT NULL,
`amount` decimal(8,2) NOT NULL,
`authCode` varchar(6) NOT NULL,
`name` varchar(50) NOT NULL,
`cardNum` varchar(22) NOT NULL,
`cardType` varchar(20) NOT NULL,
`expYear` int(4) NOT NULL,
`expMonth` int(2) NOT NULL,
`cart` text NOT NULL,
`cashier` varchar(50) NOT NULL,
PRIMARY KEY (`id`,`badge_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

alter table cc_receipts add cashier varchar(50) NOT NULL;

-- Rev 78
update violations set badge_involved=TRIM(LEADING '0' FROM badge_involved);

-- Rev 79
DROP TABLE IF EXISTS `store_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(50) NOT NULL,
  `sku` varchar(15) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `img` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `new_badge` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Rev 80
ALTER TABLE `BadgeDB`.`params`
ADD COLUMN `qb_oa2_id` VARCHAR(50) NULL DEFAULT NULL AFTER `qb_token`,
ADD COLUMN `qb_oa2_sec` VARCHAR(40) NULL DEFAULT NULL AFTER `qb_oa2_id`,
ADD COLUMN `qb_oa2_access_token` TEXT NULL DEFAULT NULL AFTER `qb_oa2_sec`,
ADD COLUMN `qb_oa2_access_date` DATETIME NULL DEFAULT NULL AFTER `qb_oa2_access_token`,
ADD COLUMN `qb_oa2_refresh_token` VARCHAR(50) NULL DEFAULT NULL AFTER `qb_oa2_access_date`,
ADD COLUMN `qb_oa2_refresh_date` DATETIME NULL DEFAULT NULL AFTER `qb_oa2_refresh_token`;

-- Rev 81
ALTER TABLE `BadgeDB`.`params`
ADD COLUMN `qb_oa2_realmId` VARCHAR(18) NULL DEFAULT NULL AFTER `qb_oa2_sec`;
ALTER TABLE `BadgeDB`.`badges`
CHANGE COLUMN `email` `email` VARCHAR(61) NOT NULL ;

-- Rev 86
DROP TABLE `BadgeDB`.`work_credit_transactions`;

ALTER TABLE `BadgeDB`.`store_items`
CHANGE COLUMN `item` `item` VARCHAR(100) NOT NULL ,
CHANGE COLUMN `sku` `sku` VARCHAR(15) NULL DEFAULT NULL ,
CHANGE COLUMN `price` `price` DECIMAL(10,2) NULL DEFAULT NULL,
ADD COLUMN `type` VARCHAR(45) NOT NULL AFTER `price`,
ADD COLUMN `parent` INT NULL DEFAULT NULL AFTER `type`;

-- Rev 87
ALTER TABLE `BadgeDB`.`fees_structure`
ADD COLUMN `sku_full` VARCHAR(15) NULL DEFAULT NULL AFTER `type`,
ADD COLUMN `sku_half` VARCHAR(15) NULL DEFAULT NULL AFTER `sku_full`;

ALTER TABLE `BadgeDB`.`cc_receipts`
CHANGE COLUMN `status` `status` VARCHAR(15) NULL DEFAULT NULL ,
CHANGE COLUMN `authCode` `authCode` VARCHAR(6) NULL DEFAULT NULL ,
CHANGE COLUMN `cardType` `cardType` VARCHAR(20) NULL DEFAULT NULL ,
CHANGE COLUMN `expYear` `expYear` INT(4) NULL DEFAULT NULL ,
CHANGE COLUMN `expMonth` `expMonth` INT(2) NULL DEFAULT NULL ,
ADD COLUMN `tx_type` VARCHAR(10) NOT NULL AFTER `badge_number`,
CHANGE COLUMN `cardNum` `cardNum` VARCHAR(22) NULL DEFAULT NULL ;

-- Rev 88
ALTER TABLE `BadgeDB`.`cc_receipts` ADD COLUMN `tx_date` DATETIME NOT NULL AFTER `badge_number`;

-- Rev 89
ALTER TABLE `BadgeDB`.`cc_receipts` ADD COLUMN `on_qb` INT(1) NOT NULL DEFAULT 0 AFTER `cashier`;

-- Rev 96
ALTER TABLE `BadgeDB`.`guest`
CHANGE COLUMN `g_city` `g_city` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `g_state` `g_state` VARCHAR(2) NULL DEFAULT NULL ;

-- Rev 100
ALTER TABLE `BadgeDB`.`violations` ADD COLUMN `vi_loc` VARCHAR(10) NOT NULL DEFAULT 'o' AFTER `vi_date`;
update BadgeDB.violations set vi_loc ='200' where vi_sum like '%200%';
update BadgeDB.violations set vi_loc ='100' where vi_sum like '%100%' and vi_loc='o';
update BadgeDB.violations set vi_loc ='50' where vi_sum like '%50%' and vi_loc='o';

-- Rev 104
CREATE TABLE `BadgeDB`.`events` (
  `e_id` INT NOT NULL AUTO_INCREMENT,
  `e_name` VARCHAR(60) NOT NULL,
  `e_date` DATE NOT NULL,
  `e_poc` INT(5) NOT NULL,
  `e_status` VARCHAR(45) NOT NULL,
  `e_type` VARCHAR(45) NOT NULL,
  `e_hours` int(5) DEFAULT NULL,
  PRIMARY KEY (`e_id`),
  UNIQUE INDEX `e_id_UNIQUE` (`e_id` ASC));

CREATE TABLE `BadgeDB`.`event_attendee` (
  `ea_id` INT NOT NULL AUTO_INCREMENT,
  `ea_event_id` INT NOT NULL,
  `ea_badge` INT(5) NULL,
  `ea_f_name` VARCHAR(45) NULL,
  `ea_l_name` VARCHAR(45) NULL,
  `ea_wb_serial` VARCHAR(10) NULL,
  PRIMARY KEY (`ea_id`),
  UNIQUE INDEX `id_ev_at_UNIQUE` (`ea_id` ASC));

-- Rev 105
ALTER TABLE `BadgeDB`.`event_attendee`
ADD COLUMN `ea_wc_logged` INT(2) NULL DEFAULT NULL AFTER `ea_wb_serial`;

UPDATE `BadgeDB`.`user_privileges` SET `priv_sort`='8' WHERE `id`='5';
INSERT INTO `BadgeDB`.`user_privileges` (`id`, `privilege`, `priv_sort`, `timeout`) VALUES ('8', 'CIO', '7', '5');

-- Rev 109
ALTER TABLE `BadgeDB`.`params`
ADD COLUMN `log_rotate` INT(3) NULL DEFAULT NULL AFTER `qb_oa2_refresh_date`;

-- Rev 117
ALTER TABLE `BadgeDB`.`violations` ADD COLUMN `vi_override` tinyint(1) not null default 0 AFTER `vi_type`;

-- Rev 120
ALTER TABLE `BadgeDB`.`events` 
ADD COLUMN `e_inst` VARCHAR(255) NULL DEFAULT NULL AFTER `e_hours`,
ADD COLUMN `e_rso` VARCHAR(60) NULL DEFAULT NULL AFTER `e_inst`;

ALTER TABLE `BadgeDB`.`user` 
ADD COLUMN `company` VARCHAR(45) NULL DEFAULT NULL AFTER `full_name`;
UPDATE BadgeDB.user SET company =`auth_key` where CHAR_LENGTH(auth_key)<>32 and privilege=8;

ALTER TABLE `BadgeDB`.`event_attendee` 
ADD COLUMN `ea_wb_out` INT(2) NULL DEFAULT 1 AFTER `ea_wc_logged`;
update BadgeDB.event_attendee set ea_wb_out=1 where ea_wb_serial<>'';

-- Rev 129
CREATE TABLE `BadgeDB`.`mass_email` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mass_to` VARCHAR(255) NOT NULL,
  `mass_subject` VARCHAR(255) NOT NULL,
  `mass_body` BLOB NOT NULL,
  `mass_created` DATETIME NULL,
  `mass_created_by` INT NULL,
  `mass_updated` DATETIME NULL,
  `mass_updated_by` INT NULL,
  `mass_running` INT NULL DEFAULT 0,
  `mass_start` DATETIME NULL,
  `mass_runtime` DATETIME NULL,
  `mass_lastbadge` INT NULL,
  `mass_finished` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));

-- Rev 138
ALTER TABLE `BadgeDB`.`params` 
ADD COLUMN `qb_env` VARCHAR(4) NULL DEFAULT 'dev' AFTER `status`;

-- Rev 142
ALTER TABLE `BadgeDB`.`params` 
ADD COLUMN `pp_id` VARCHAR(82) NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `pp_sec` VARCHAR(82) NULL DEFAULT NULL AFTER `pp_id`;

-- Rev 146
ALTER TABLE `BadgeDB`.`cc_receipts` 
CHANGE COLUMN `id` `id` VARCHAR(32) NOT NULL DEFAULT '' ,
CHANGE COLUMN `name` `name` VARCHAR(128) NOT NULL ;

-- Rev 147
ALTER TABLE `BadgeDB`.`clubs` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL ,
CHANGE COLUMN `club_id` `club_id` INT(11) NOT NULL AUTO_INCREMENT ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`club_id`);
ALTER TABLE `BadgeDB`.`clubs` DROP COLUMN `id`;

-- Rev 151
ALTER TABLE `BadgeDB`.`mass_email` 
ADD COLUMN `mass_reply_to` VARCHAR(255) NULL DEFAULT NULL AFTER `mass_to`;

-- Rev 156
ALTER TABLE `BadgeDB`.`guest` 
  ADD COLUMN `g_paid` VARCHAR(1) NULL DEFAULT 0 AFTER `g_yob`,
  CHANGE COLUMN `tmp_badge` `tmp_badge` INT(6) NULL;
ALTER TABLE `BadgeDB`.`params` 
  ADD COLUMN `guest_sku` INT(6) NOT NULL DEFAULT 460130 AFTER `sell_date`,
  ADD COLUMN `guest_total` INT(3) NOT NULL DEFAULT 50 AFTER `guest_sku`;
ALTER TABLE `BadgeDB`.`cc_receipts` 
  ADD COLUMN `guest_cred` INT(3) NULL DEFAULT 0 AFTER `cart`;
  
-- Rev 164
ALTER TABLE `BadgeDB`.`user` 
ADD COLUMN `clubs` VARCHAR(255) NULL DEFAULT NULL AFTER `updated_at`;

-- Rev 169
SELECT @@SESSION.sql_mode;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
update associat_agcnew.agc_calendar set recurrent_start_date = null where recurrent_start_date = '0000-00-00 00:00:00';
update associat_agcnew.agc_calendar set recurrent_end_date = null where recurrent_end_date = '0000-00-00 00:00:00';
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

ALTER TABLE `associat_agcnew`.`agc_calendar` 
CHANGE COLUMN `poc_name` `poc_name` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `poc_phone` `poc_phone` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `poc_email` `poc_email` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `conflict` `conflict` TINYINT(11) NULL DEFAULT '0' ,
CHANGE COLUMN `recur_every` `recur_every` INT(11) NULL DEFAULT '0' ;

update associat_agcnew.agc_calendar set recurrent_calendar_id = calendar_id where recurrent_calendar_id=0 and recur_every=1;
update associat_agcnew.agc_calendar set deleted=1 where recurrent_calendar_id = calendar_id and recur_week_days='';

-- Rev 178 v2.1.1
ALTER TABLE `BadgeDB`.`user` 
CHANGE COLUMN `privilege` `privilege` VARCHAR(45) NOT NULL ;
INSERT INTO BadgeDB.user_privileges VALUES ('11', 'Calendar Close', '81', '10');
