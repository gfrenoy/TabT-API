<?php
// **************************************************************************
// Copyright (C) 2001-2015 Gaëtan Frenoy (gaetan [à] frenoy.net)
// **************************************************************************
// This file is part of « TabT-DB »
// a software to manage a database of a table tennis association.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details:
//                 http://www.gnu.org/copyleft/gpl.html
// ***************************************************************************

//
// Attention: this file can be included multiple times
// we need to make sure we have not defined those functions yet
//
if (!function_exists('dict_add')) {
  // Add an entry to the global dictionary
  function dict_add($key, $dict_entry = null) {
    if (is_null($dict_entry)) $dict_entry = dict_load_entry($key);
    if (is_array($dict_entry) && count($dict_entry)>0) {
      $GLOBALS['dictionary'][$key] = $dict_entry;
    }
  }

  // Loads a dictionary entry from an external file
  function dict_load_entry($key) {
    // Sanitize input key
    $key = strtr($key, "./ \\", "____");
    $entry = null;
    $filename = "../public/i18n/{$key}.txt";
    if (file_exists($filename)) {
      $content = file_get_contents($filename);
      if (preg_match_all('#\[(?P<lang>[a-z]{2})\](?<content>.+)\[/(?:[a-z]{2})\]#msU', $content, $m)) {
        $entry = array();
        foreach ($m['lang'] as $i => $v) {
          $c = $m['content'][$i];
          if (preg_match_all('#\[img=(.+)/\]#msU', $c, $match_img)) {
            foreach ($match_img[0] as $ii => $vv) {
              $c = str_replace($vv, $GLOBALS['style_vars'][$match_img[1][$ii]], $c);
            }
          }
          $entry[$v] = trim($c);
        }
      }
    }
    return $entry;
  }

  // Compute dictionary entries
  function dict_compute() {
    foreach ($GLOBALS['dictionary'] as $key => $dict_entry) {
      $name = 'str_' . $key;
      if (isset($dict_entry[$GLOBALS['lang']])) {
        $GLOBALS[$name] = $dict_entry[$GLOBALS['lang']];
      } else if (isset($GLOBALS['site_info']['default_language']) && isset($dict_entry[$GLOBALS['site_info']['default_language']])) {
        $GLOBALS[$name] = $dict_entry[$GLOBALS['site_info']['default_language']];
      } else {
        unset($GLOBALS[$name]);
      }
      // Processed, we can forget it (so we can call dict_compute again...)
      unset($GLOBALS['dictionary'][$key]);
    }
  }
}

// Shall we use a cache system?
$use_cache = false;

// Defines the system language
if (!isset($lang) || !in_array($lang, array('fr', 'nl', 'en', 'mk'))) {
  if (isset($GLOBALS['site_info']['default_language'])) {
    $lang = $GLOBALS['site_info']['default_language'];
  } else {
    $lang = 'en';
  }
}

//
// Localization of URL
//
$URL_OBJECTS = array(
  'home' => array(
    'fr' => 'accueil',
    'nl' => 'welkom',
    'en' => 'home',
    'mk' => 'Дома'
  ),
  'clubs' => array(
    'fr' => 'clubs',
    'nl' => 'clubs',
    'en' => 'clubs',
    'mk' => 'клубови'
  ),
  'club' => array(
    'fr' => 'club',
    'nl' => 'club',
    'en' => 'club',
    'mk' => 'Клуб'
  ),
  'divisions' => array(
    'fr' => 'divisions',
    'nl' => 'afdelingen',
    'en' => 'divisions',
    'mk' => 'лиги'
  ),
  'division' => array(
    'fr' => 'division',
    'nl' => 'afdeling',
    'en' => 'division',
    'mk' => 'лига'
  ),
  'calendars' => array(
    'fr' => 'calendriers',
    'nl' => 'kalenders',
    'en' => 'calendars',
    'mk' => 'календари'
  ),
  'calendars/grid' => array(
    'fr' => 'calendriers/grille',
    'nl' => 'kalenders/rooster',
    'en' => 'calendars/grid',
    'mk' => 'календари/мрежа'
  ),
  'calendars/dates' => array(
    'fr' => 'calendriers/dates',
    'nl' => 'kalenders/data',
    'en' => 'calendars/dates',
    'mk' => 'календари/датуми'
  ),
  'calendar' => array(
    'fr' => 'calendrier',
    'nl' => 'kalender',
    'en' => 'calendar',
    'mk' => 'календар'
  ),
  'results' => array(
    'fr' => 'resultats',
    'nl' => 'uitslagen',
    'en' => 'results'
  ),
  'match' => array(
    'fr' => 'match',
    'nl' => 'wedstrijd',
    'en' => 'match',
    'mk' => 'меч'
  ),
  'rankings' => array(
    'fr' => 'classements',
    'nl' => 'rangschikkingen',
    'en' => 'rankings'
  ),
  'members' => array(
    'fr' => 'membres',
    'nl' => 'leden',
    'en' => 'members',
    'mk' => 'играчи'
  ),
  'players' => array(
    'fr' => 'joueurs',
    'nl' => 'spelers',
    'en' => 'players',
    'mk' => 'играчи'
  ),
  'player' => array(
    'fr' => 'joueur',
    'nl' => 'speler',
    'en' => 'player',
    'mk' => 'играч'
  ),
  'player_result' => array(
    'fr' => 'resultats',
    'nl' => 'uitslagen',
    'en' => 'results'
  ),
  'tournaments' => array(
    'fr' => 'tournois',
    'nl' => 'tornooien',
    'en' => 'tournaments',
    'mk' => 'турнири'
  ),
  'tournament' => array(
    'fr' => 'tournoi',
    'nl' => 'tornooi',
    'en' => 'tournament',
    'mk' => 'турнир'
  ),
  'tournament/registration' => array(
    'fr' => 'tournoi/inscription',
    'nl' => 'tornooi/inschrijving',
    'en' => 'tournament/registration',
    'mk' => 'турнир/регистрација'
  ),
  'admin' => array(
    'fr' => 'admin',
    'nl' => 'admin',
    'en' => 'admin',
    'mk' => 'aдмин'
  ),
);

//
// Main dictionary
//
if ($use_cache && function_exists('apc_fetch') && !isset($_GET['clearcache'])) {
  if ($dictionary = apc_fetch('dictionary')) {
    return;
  }
}

$dictionary = array();
dict_add('Keyword', array(
  'fr' => 'FR',
  'nl' => 'NL',
  'en' => 'EN',
  'mk' => 'MK',
));
dict_add('Main', array(
  'fr' => 'Accueil',
  'nl' => 'Home',
  'en' => 'Home',
  'mk' => 'Дома',
));
dict_add('Clubs', array(
  'fr' => 'Clubs',
  'nl' => 'Clubs',
  'en' => 'Clubs',
  'mk' => 'Клубови',
));
dict_add('Divisions', array(
  'fr' => 'Divisions',
  'nl' => 'Afdelingen',
  'en' => 'Divisions',
  'mk' => 'Дивизија',
));
dict_add('Calendar', array(
  'fr' => 'Calendrier',
  'nl' => 'Kalender',
  'en' => 'Calendar',
  'mk' => 'Календар',
));
dict_add('Teams', array(
  'fr' => 'Équipes',
  'nl' => 'Ploegen',
  'en' => 'Teams',
  'mk' => 'Тимови',
));
dict_add('Team', array(
  'fr' => 'Équipe',
  'nl' => 'Ploeg',
  'en' => 'Team',
  'mk' => 'Тим',
));
dict_add('Matches', array(
  'fr' => 'Rencontres',
  'nl' => 'Ontmoetingen',
  'en' => 'Match',
  'mk' => 'Меч',
));
dict_add('Results', array(
  'fr' => 'Résultats',
  'nl' => 'Resultaten',
  'en' => 'Results',
  'mk' => 'Резултати',
));
dict_add('Classement', array(
  'fr' => 'Classement',
  'nl' => 'Rangschikking',
  'en' => 'Ranking',
  'mk' => 'Ранг',
));
dict_add('Classements', array(
  'fr' => 'Classements',
  'nl' => 'Rangschikkingen',
  'en' => 'Rankings',
  'mk' => 'Рангирања',
));
dict_add('PlayerClassement', array(
  'fr' => 'Classement',
  'nl' => 'Klassering',
  'en' => 'Ranking',
  'mk' => 'Рангирање',
));
dict_add('Players', array(
  'fr' => 'Joueurs',
  'nl' => 'Spelers',
  'en' => 'Players',
  'mk' => 'Играчи',
));
dict_add('PlayersMenu', array(
  'fr' => 'Membres',
  'nl' => 'Leden',
  'en' => 'Members',
  'mk' => 'Членови',
));
dict_add('ViewMembers', array(
  'fr' => "Liste des membres",
  'nl' => 'Ledenlijst',
  'en' => 'Member list',
  'mk' => 'Членови',
));
dict_add('Back', array(
  'fr' => 'Retour',
  'nl' => 'Terug',
  'en' => 'Back',
  'mk' => 'Назад',
));
dict_add('ReallyWant', array(
  'fr' => 'Voulez vous vraiment ',
  'nl' => 'Wil je werkelijk ',
  'en' => 'Do you really want to',
  'mk' => 'Дали навистина сакаш да',
));
dict_add('Season', array(
  'fr' => 'Saison',
  'nl' => 'Seizoen',
  'en' => 'Season',
  'mk' => 'Сезона',
));
dict_add('season', array(
  'fr' => 'saison',
  'nl' => 'seizoen',
  'en' => 'season',
  'mk' => 'сезона',
));
dict_add('Type', array(
  'fr' => 'Type',
  'nl' => 'Type',
  'en' => 'Type',
  'mk' => 'Тип',
));
dict_add('PerWeek', array(
  'fr' => 'Par Semaine',
  'nl' => 'Per Week',
  'en' => 'Per Week',
  'mk' => 'По Недела',
));
dict_add('PerTeam', array(
  'fr' => 'Par Équipe',
  'nl' => 'Per Ploeg',
  'en' => 'Per Team',
  'mk' => 'По Тим',
));
dict_add('PerClub', array(
  'fr' => 'Par Club',
  'nl' => 'Per Club',
  'en' => ' Per Club',
  'mk' => 'По Клуб',
));
dict_add('PerClassement', array(
  'fr' => 'Par Classement',
  'nl' => 'Per Klassement',
  'en' => 'Per Ranking',
  'mk' => 'По Ранг',
));
dict_add('PerELO', array(
  'fr' => 'Par points ELO',
  'nl' => 'Per ELO-punten',
  'en' => 'Per ELO points',
  'mk' => 'По ELO поени',
));
dict_add('DownloadPDF', array(
  'fr' => 'Télécharger PDF',
  'nl' => 'Download PDF',
  'en' => 'Download PDF',
  'mk' => 'Преземи PDF',
));
dict_add('ToPrint', array(
  'fr' => 'Pour Imprimer',
  'nl' => 'Afdrukken',
  'en' => 'To Print',
  'mk' => 'Печати',
));
dict_add('AddLocal', array(
  'fr' => 'Ajouter Local',
  'nl' => 'Adres toevoegen',
  'en' => 'Add address',
  'mk' => 'Додади адреса',
));
dict_add('AddLocalConfirm', array(
  'fr' => 'Voulez-vous réellement ajouter un local',
  'nl' => 'Wilt U werkelijk een adres toevoegen',
  'en' => 'Do you really want to add an address',
  'mk' => 'Дали навистина сакаш да додадеш адреса',
));
dict_add('RemLocal', array(
  'fr' => 'Supprimer Local',
  'nl' => 'Adres verwijderen',
  'en' => 'Delete address',
  'mk' => 'Избриши адреса',
));
dict_add('RemLocalConfirm', array(
  'fr' => 'Voulez-vous réellement supprimer le dernier local',
  'nl' => 'Wilt U werkelijk de laatste adres verwijderen',
  'en' => 'Do you really want to delete the last address',
  'mk' => 'Дали навистина сакаш да избришеш адреса',
));
dict_add('AddResponsible', array(
  'fr' => 'Ajouter Responsable',
  'nl' => 'Verantwoordelijke toevoegen',
  'en' => 'Add responsible',
  'mk' => 'Додади одговорен',
));
dict_add('AddResponsibleConfirm', array(
  'fr' => 'Voulez-vous réellement ajouter un responsable',
  'nl' => 'Wilt U werkelijk een nieuwe verantwoordelijke toevoegen',
  'en' => 'Do you really want to add a new responsible',
  'mk' => 'Дали навистина сакаш да додадеш нов одговорен',
));
dict_add('RemResponsible', array(
  'fr' => 'Supprimer Responsable',
  'nl' => 'Verantwoordelijke verwijderen',
  'en' => 'Delete responsible',
  'mk' => 'Избриши одговорен',
));
dict_add('RemResponsibleConfirm', array(
  'fr' => 'Voulez-vous réellement supprimer le dernier responsable',
  'nl' => 'Wilt U werkelijk de laatste verantwoordelijke verwijderen',
  'en' => 'Do you really want to delete the last responsible',
  'mk' => 'Дали навистина сакаш да избришеш одговорен',
));
dict_add('Cancel', array(
  'fr' => 'Annuler',
  'nl' => 'Annuleren',
  'en' => 'Cancel',
  'mk' => 'Прекини',
));
dict_add('Dates', array(
  'fr' => 'Dates',
  'nl' => 'Datums',
  'en' => 'Dates',
  'mk' => 'Датуми',
));
dict_add('View', array(
  'fr' => 'Consulter',
  'nl' => 'Raadplegen',
  'en' => 'View',
  'mk' => 'Прикажи',
));
dict_add('DivisionList', array(
  'fr' => 'Liste des divisions',
  'nl' => 'Lijst van de afdelingen',
  'en' => 'Divisions list',
  'mk' => 'Листа на дивизии',
));
dict_add('All', array(
  'fr' => 'Tous',
  'nl' => 'Alle',
  'en' => 'All',
  'mk' => 'Сите',
));
dict_add('AllF', array(
  'fr' => 'Toutes',
  'nl' => 'Alle Dames',
  'en' => 'All Women',
  'mk' => 'Сите Жени',
));
dict_add('AllCategories', array(
  'fr' => 'Toutes',
  'nl' => 'Alle',
  'en' => 'All',
  'mk' => 'Сите',
));
dict_add('AllDivisions', array(
  'fr' => 'Toutes',
  'nl' => 'Alle',
  'en' => 'All',
  'mk' => 'Сите',
));
dict_add('DivisionEmpty', array(
  'fr' => 'Aucune division ne correspond au critère(s) choisi(s).',
  'nl' => 'Geen enkele afdeling beantwoordt aan de vereiste(n)',
  'en' => 'There is no division that matches the criteria',
  'mk' => 'Нема дивизии кои го задоволуваат условот',
));
dict_add('Actions', array(
  'fr' => 'Actions',
  'nl' => 'Acties',
  'en' => 'Actions',
  'mk' => 'Акции',
));
dict_add('Add', array(
  'fr' => 'Ajouter',
  'nl' => 'Toevoegen',
  'en' => 'Add',
  'mk' => 'Додади',
));
dict_add('Modify', array(
  'fr' => 'Modifier',
  'nl' => 'Wijzigen',
  'en' => 'Modify',
  'mk' => 'Измени',
));
dict_add('Hide', array(
  'fr' => 'Cacher',
  'nl' => 'Verbergen',
  'en' => 'Hide',
  'mk' => 'Сокриј',
));
dict_add('Goto', array(
  'fr' => 'Résultats',
  'nl' => 'Resultaten',
  'en' => 'Results',
  'mk' => 'Резултати',
));
dict_add('Composition', array(
  'fr' => 'Composition',
  'nl' => 'Samenstelling',
  'en' => 'Roster',
  'mk' => 'Состав',
));
dict_add('Delete', array(
  'fr' => 'Effacer',
  'nl' => 'Verwijderen',
  'en' => 'Delete',
  'mk' => 'Избриши',
));
dict_add('Verify', array(
  'fr' => 'Vérifier',
  'nl' => 'Checken',
  'en' => 'Check',
  'mk' => 'Провери',
));
dict_add('Week', array(
  'fr' => 'Semaine',
  'nl' => 'Week',
  'en' => 'Week',
  'mk' => 'Недела',
));
dict_add('Division', array(
  'fr' => 'Division',
  'nl' => 'Afdeling',
  'en' => 'Division',
  'mk' => 'Дивизија',
));
dict_add('Serie', array(
  'fr' => 'Série',
  'nl' => 'Reeks',
  'en' => 'Serie',
  'mk' => 'Серија',
));
dict_add('Series', array(
  'fr' => 'Séries',
  'nl' => 'Reeksen',
  'en' => 'Series',
  'mk' => 'Серии',
));
dict_add('Level', array(
  'fr' => 'Niveau',
  'nl' => 'Niveau',
  'en' => 'Level',
  'mk' => 'Ниво',
));
dict_add('Category', array(
  'fr' => 'Catégorie',
  'nl' => 'Categorie',
  'en' => 'Category',
  'mk' => 'Категорија',
));
dict_add('CategoryShort', array(
  'fr' => 'Cat.',
  'nl' => 'Cat.',
  'en' => 'Cat.',
  'mk' => 'Кат.',
));
dict_add('Province', array(
  'fr' => 'Province',
  'nl' => 'Provincie',
  'en' => 'Province',
  'mk' => '',
));
dict_add('CalendarType', array(
  'fr' => 'Type de calendrier',
  'nl' => 'Type kalender',
  'en' => 'Calendar type',
  'mk' => 'Тип на календар',
));
dict_add('FirstMatchNumber', array(
  'fr' => 'Numéro du premier match',
  'nl' => 'Nummer van de eerste wedstrijd',
  'en' => 'Number of the first match',
  'mk' => 'Број на првиот натпревар',
));
dict_add('MatchNumberScheme', array(
  'fr' => 'Format du numéro de match',
  'nl' => 'Wedstrijdsnummer schema',
  'en' => 'Match number scheme',
  'mk' => 'Нумерација на натпревари - формат',
));
dict_add('Properties', array(
  'fr' => 'Propriétés',
  'nl' => 'Eigenschappen',
  'en' => 'Properties',
  'mk' => 'Својства',
));
dict_add('Value', array(
  'fr' => 'Valeur',
  'nl' => 'Waarde',
  'en' => 'Value',
  'mk' => 'Вредност',
));
dict_add('Values', array(
  'fr' => 'Valeurs',
  'nl' => 'Waarden',
  'en' => 'Values',
  'mk' => 'Вредности',
));
dict_add('LocalId', array(
  'fr' => 'Local',
  'nl' => 'Lokaal',
  'en' => 'Address',
  'mk' => 'Адреса',
));
dict_add('Indice', array(
  'fr' => 'Indice',
  'nl' => 'Index',
  'en' => 'Indice',
  'mk' => 'Индекс',
));
dict_add('ClubIndex', array(
  'fr' => 'Numéro d\'affiliation',
  'nl' => 'Aansluitingsnummer',
  'en' => 'Club Id',
  'mk' => 'Индекс на клубот',
));
dict_add('Club', array(
  'fr' => 'Club',
  'nl' => 'Club',
  'en' => 'Club',
  'mk' => 'Клуб',
));
dict_add('ClubShortName', array(
  'fr' => 'Nom court',
  'nl' => 'Korte naam',
  'en' => 'Short name',
  'mk' => 'Кратко име',
));
dict_add('Address', array(
  'fr' => 'Adresse',
  'nl' => 'Adres',
  'en' => 'Address',
  'mk' => 'Адреса',
));
dict_add('Position', array(
  'fr' => 'Position',
  'nl' => 'Plaats',
  'en' => 'Position',
  'mk' => 'Позиција',
));
dict_add('Modified', array(
  'fr' => 'Modification enregistrée.',
  'nl' => 'Wijziging opgeslagen.',
  'en' => 'Record updated.',
  'mk' => 'Записот е изменет',
));
dict_add('AddressIdModified', array(
  'fr' => 'Le numéro du local n\'est pas valide pour le club sélectionné.<br>Le premier local sera utilisé par défaut.<br>',
  'nl' => 'Het lokaalnummer is niet geldig.<br>Het eerste lokaal zal gebruikt worden.<br>',
  'en' => 'The local number is invalid for the selected club.<br>The first local will be used by default.<br>',
  'mk' => 'Бројот не е валиден за избраниот клуб.<br>Ќе се употреби преддефинираниот.<br>',
));
dict_add('BadHourFormat', array(
  'fr' => 'Le format de l\'heure n\'est pas correct.<br>Veuillez respecter le format HH:MM où HH est compris entre 00 et 23 et MM entre 00 et 59<br>',
  'nl' => 'Het formaat voor het uur is ongeldig.<br>Gelieve volgend formaat te eerbiedigen: HH:MM met HH tussen 00 en 23 en MM tussen 00 en 59<br>',
  'en' => 'The hour format is invalid.<br>Please use the following format HH:MM where HH is between 00 and 23 and MM between 00 and 59<br>',
  'mk' => 'Форматот на времето не е валиден.<br>Ве молам внесе време во формат ЧЧ:ММ каде ЧЧ е часот помеѓу 00 и 23, а ММ минутите помеѓу 00 и 59.<br>',
));
dict_add('Hour', array(
  'fr' => 'Heure',
  'nl' => 'Uur',
  'en' => 'Hour',
  'mk' => 'Час',
));
dict_add('DayInWeek', array(
  'fr' => 'Jour',
  'nl' => 'Dag',
  'en' => 'Day',
  'mk' => 'Ден',
));
dict_add('Monday', array(
  'fr' => 'Lundi',
  'nl' => 'Maandag',
  'en' => 'Monday',
  'mk' => 'Понеделник',
));
dict_add('Tuesday', array(
  'fr' => 'Mardi',
  'nl' => 'Dinsdag',
  'en' => 'Tuesday',
  'mk' => 'Вторник',
));
dict_add('Wednesday', array(
  'fr' => 'Mercredi',
  'nl' => 'Woensdag',
  'en' => 'Wednesday',
  'mk' => 'Среда',
));
dict_add('Thursday', array(
  'fr' => 'Jeudi',
  'nl' => 'Donderdag',
  'en' => 'Thursday',
  'mk' => 'Четврток',
));
dict_add('Friday', array(
  'fr' => 'Vendredi',
  'nl' => 'Vrijdag',
  'en' => 'Friday',
  'mk' => 'Петок',
));
dict_add('Sathurday', array(
  'fr' => 'Samedi',
  'nl' => 'Zaterdag',
  'en' => 'Saturday',
  'mk' => 'Сабота',
));
dict_add('Sunday', array(
  'fr' => 'Dimanche',
  'nl' => 'Zondag',
  'en' => 'Sunday',
  'mk' => 'Недела',
));
dict_add('MatchNum', array(
  'fr' => 'Match',
  'nl' => 'Wedstrijd',
  'en' => 'Game',
  'mk' => 'Меч',
));
dict_add('Date', array(
  'fr' => 'Date',
  'nl' => 'Datum',
  'en' => 'Date',
  'mk' => 'Датум',
));
dict_add('HomeTeam', array(
  'fr' => 'Visités',
  'nl' => 'Thuis',
  'en' => 'Home',
  'mk' => 'Домаќин',
));
dict_add('AwayTeam', array(
  'fr' => 'Visiteurs',
  'nl' => 'Bezoekers',
  'en' => 'Away',
  'mk' => 'Гостин',
));
dict_add('Score', array(
  'fr' => 'Score',
  'nl' => 'Score',
  'en' => 'Score',
  'mk' => 'Резултат',
));
dict_add('ShowMapWithMaporama', array(
  'fr' => 'Voir le plan avec Maporama',
  'nl' => 'Plan bekijken met Maporama',
  'en' => 'See the map with Maporama',
  'mk' => 'Види ја мапата преку Maporama',
));
dict_add('Map', array(
  'fr' => 'Plan',
  'nl' => 'Plan',
  'en' => 'Map',
  'mk' => 'Мапа',
));
dict_add('Configure', array(
  'fr' => 'Configurer',
  'nl' => 'Configureren',
  'en' => 'Configure',
  'mk' => 'Конфигурирај',
));
dict_add('ConfigureShort', array(
  'fr' => 'Config.',
  'nl' => 'Config.',
  'en' => 'Config.',
  'mk' => 'Конфиг.',
));
dict_add('NoCalendarDates', array(
  'fr' => 'Aucune date n\'est disponible pour ce calendrier pour la saison sélectionnée',
  'nl' => 'Geen enkele datum is beschikbaar voor deze kalender voor het gekozen seizoen',
  'en' => 'There is no date available for this calendar in the selected season.',
  'mk' => 'Нема слободен датум за овој календар во избраната сезона.',
));
dict_add('DateFormatExplain', array(
  'fr' => 'Le format de la date est JJ-MM-AAAA où JJ est le jour (2 chiffres de 01 à 31), MM est le mois (2 chiffres de 01 à 12) et AAAA l\'année (4 chiffres).  Les tirets (-) sont facultatifs.',
  'nl' => 'Het datumformaat is DD-MM-JJJJ met DD als dag (2 cijfers van 01 tot 31), MM als maand (2 cijfers van 01 tot 12) en JJJJ als jaar (4 cijfers).  Streepjes (-) zijn facultatief.',
  'en' => 'The date format is DD-MM-YYYY where DD is the day (2 digits from 01 to 31), MM is the month (2 digits from 01 to 12) and YYYY the year (4 digits).  Dashes (-) are optional.',
  'mk' => 'Форматот на датумот е ДД-ММ-ГГГГ каде што ДД е ден (2 цифри помеѓу 01 и 31), ММ е месец (2 цифри помеѓу 01 и 12), а ГГГГ е година (4 цифри). Цртичките (-) се незадолжителни.',
));
dict_add('TimeFormatExplain', array(
  'fr' => 'Le format du temps est HH:MM où HH est l\'heure (2 chiffres de 00 à 23) et MM les minutes (2 chiffres de 00 à 59).',
  'nl' => 'Het uurformaat is HH:MM met HH als uren (2 cijfers van 00 tot 23) en MM als minuten (2 cijfers van 00 tot 59)',
  'en' => 'The time format is HH:MM where HH for hours (2 digits from 00 to 23) en MM for minutes (2 digits from 00 to 59)',
  'mk' => 'Фроматот на времето е ЧЧ:ММ каде што ЧЧ се часови (2 цифри помеѓу 00 и 23), а ММ се минути (2 цифри помеѓу 00 и 59)',
));
dict_add('CalendarDatesExplain', array(
  'fr' => '<b>Important</b>:<br><br><li>La date indiquée correspond au SAMEDI de la semaine d\'interclubs.</li><br><br><li>Le format de la date est JJ-MM-AAAA où JJ est le jour (2 chiffres de 01 à 31), MM est le mois (2 chiffres de 01 à 12) et AAAA l\'année (4 chiffres).  Les tirets (-) sont facultatifs.</li>',
  'nl' => '<b>Belangrijk</b>:<br><br><li>De vermelde datum stemt overeen met de zaterdag van de interclubweek.</li><br><br><li>Het datumformaat is DD-MM-JJJJ met DD als dag (2 cijfers van 01 tot 31), MM als maand (2 cijfers van 01 tot 12) en JJJJ als jaar (4 cijfers).  Streepjes (-) zijn facultatief.</li>',
  'en' => '<b>Important</b>:<br><br><li>The date corresponds to SATHURDAY.</li><br><br><li>The date format is DD-MM-YYYY where DD is the day (2 digits from 01 to 31), MM is the month (2 digits from 01 to 12) and YYYY the year (4 digits).  Dashes (-) are optional.</li>',
  'mk' => '<b>Важно</b>:<br><br><li>Датумот одговара на САБОТА.</li><br><br><li>Форматот на датумот е ДД-ММ-ГГГГ каде што ДД е ден (2 цифри помеѓу 01 и 31), ММ е месец (2 цифри помеѓу 01 и 12), а ГГГГ е година (4 цифри). Цртичките (-) се незадолжителни.</li>',
));
dict_add('CreateCalendarDates', array(
  'fr' => 'Créer les dates',
  'nl' => 'Datums creëren',
  'en' => 'Create dates',
  'mk' => 'Креирај датуми',
));
dict_add('Site', array(
  'fr' => 'URL site du club',
  'nl' => 'URL van de club',
  'en' => 'Club site URL',
  'mk' => 'Веб адреса на клубот',
));
dict_add('AdminName', array(
  'fr' => 'Nom de la personne de contact',
  'nl' => 'Naam van de contactpersoon',
  'en' => 'Name of the club contact',
  'mk' => 'Име на администраторот',
));
dict_add('AdminMail', array(
  'fr' => 'Mail du club',
  'nl' => 'E-mail van de club',
  'en' => 'e-Mail of the club',
  'mk' => 'E-mail адреса на клубот',
));
dict_add('French', array(
  'fr' => 'Site en Français',
  'nl' => 'Site in het Frans',
  'en' => 'Site in French',
  'mk' => 'Вебсајт на француски',
));
dict_add('Dutch', array(
  'fr' => 'Site in het Nederlands',
  'nl' => 'Site in het Nederlands',
  'en' => 'Site in het Nederlands',
  'mk' => 'Вебсајт на холандски',
));
dict_add('German', array(
  'fr' => 'Site en Allemand',
  'nl' => 'Site in het Duits',
  'en' => 'Site in German',
  'mk' => 'Вебсајт на германски',
));
dict_add('English', array(
  'fr' => 'Site en Anglais',
  'nl' => 'Site in het Engels',
  'en' => 'Вебсајт на англиски',
  'mk' => '',
));
dict_add('DeadLink', array(
  'fr' => 'Lien invalide',
  'nl' => 'Ongeldige link',
  'en' => 'Dead link',
  'mk' => 'Непостоечки линк',
));
dict_add('Local', array(
  'fr' => 'Local',
  'nl' => 'Lokaal',
  'en' => 'Address',
  'mk' => 'Адреса',
));
dict_add('LocalName', array(
  'fr' => 'Nom du local',
  'nl' => 'Naam van het lokaal',
  'en' => 'Name of the place',
  'mk' => 'Име на место',
));
dict_add('LocalAdr', array(
  'fr' => 'Adresse du local',
  'nl' => 'Adres van het lokaal',
  'en' => 'Address',
  'mk' => 'Адреса на местото',
));
dict_add('ZipCode', array(
  'fr' => 'Code postal',
  'nl' => 'Postcode',
  'en' => 'Zip code',
  'mk' => 'Поштенски број',
));
dict_add('LocalTown', array(
  'fr' => 'Ville du local',
  'nl' => 'Gemeente',
  'en' => 'Town',
  'mk' => 'Град',
));
dict_add('LocalPhone', array(
  'fr' => 'Téléphone',
  'nl' => 'Telefoonnummer',
  'en' => 'Phone',
  'mk' => 'Телефон',
));
dict_add('LocalAlternativePhone', array(
  'fr' => 'Téléphone (autre)',
  'nl' => 'Telefoonnummer (andere)',
  'en' => 'Phone (other)',
  'mk' => 'Телефон (други)',
));
dict_add('LocalFax', array(
  'fr' => 'Fax',
  'nl' => 'Faxnummer',
  'en' => 'Fax',
  'mk' => 'Факс',
));
dict_add('LocalComment', array(
  'fr' => 'Commentaire sur le local',
  'nl' => 'Commentaar betreffende het lokaal',
  'en' => 'Comments',
  'mk' => 'Коментари',
));
dict_add('NewLocalAdded', array(
  'fr' => 'Un nouveau local a été ajouté pour ce club.  Veuillez remplir les nouveaux champs.',
  'nl' => 'Een nieuw lokaal werd toegevoegd voor deze club. Gelieve de nieuwe velden in te vullen.',
  'en' => 'A new address has been added to the club record.  Please fill the new fields.',
  'mk' => 'Нова адреса е додадена во податоците за клубот. Ве молам пополнете ги новите полиња.',
));
dict_add('LastLocalDeleted', array(
  'fr' => 'Le dernier local disponible a été supprimé.',
  'nl' => 'Het laatste lokaal werd geschrapt.',
  'en' => 'The last address has been deleted.',
  'mk' => 'Последната адреса е избришана.',
));
dict_add('LastLocalNOTDeleted', array(
  'fr' => 'Impossible de supprimer un local.',
  'nl' => 'Onmogelijk het laatste lokaal te schrappen.',
  'en' => 'Unable to delete the address.',
  'mk' => 'Неможам да ја избришам адресата.',
));
dict_add('NewResponsibleAdded', array(
  'fr' => 'Un nouveau responsable a été ajouté pour ce club.  Veuilez modifier le nom de ce responsable.',
  'nl' => 'Een nieuwe verantwoordelijke werd toegevoegd voor deze club.  Gelieve de naam van de verantwoordelijke te veranderen.',
  'en' => 'A new responsible has been added for this club.  Please update name of this responsible.',
  'mk' => 'Ново одговорно лице е додадено за овој клуб. Ве молам ажурирајте го името на одговорното лице.',
));
dict_add('NewResponsibleRemoved', array(
  'fr' => 'Le dernier responsable disponible a été supprimé.',
  'nl' => 'De laatste verantwoordelijke werd geschrapt.',
  'en' => 'The lasts responsible has been deleted.',
  'mk' => 'Последното одговорно лице е избришано.',
));
dict_add('NewResponsibleNOTRemoved', array(
  'fr' => 'Impossible de supprimer un responsable.',
  'nl' => 'Onmogelijk de laatste verantwoordelijke te schrappen.',
  'en' => 'Unable to delete last responsible.',
  'mk' => 'Неможам да го избришам одговорното лице.',
));
dict_add('ChangeMe', array(
  'fr' => '*=- Changez moi -=*',
  'nl' => '*=- Wijzig mij -=*',
  'en' => '*=- Please Modify -=*',
  'mk' => '*=- промени -=*',
));
dict_add('NewClubAdded', array(
  'fr' => 'Un nouveau club a été ajouté.  Veuillez remplir les nouveaux champs.',
  'nl' => 'Een nieuwe club werd toegevoegd. Gelieve de nieuwe velden in te vullen.',
  'en' => 'A new club has been added.  Please fill the new fields.',
  'mk' => 'Додаден е нов клуб. Ве молам пополнете ги новите полиња.',
));
dict_add('DeleteDone', array(
  'fr' => 'a été correctement effacé.',
  'nl' => 'werd correct verwijderd.',
  'en' => 'has been correctly deleted.',
  'mk' => 'е успешно избришан.',
));
dict_add('FirstSeason', array(
  'fr' => 'Première saison d\'activité',
  'nl' => 'Eerste actief seizoen.',
  'en' => 'First seaon of activity',
  'mk' => 'Прова активна сезона',
));
dict_add('LastSeason', array(
  'fr' => 'Dernière saison d\'activité',
  'nl' => 'Laatste actief seizoen',
  'en' => 'Last season of activity',
  'mk' => 'Последна активна сезона',
));
dict_add('HasAlwaysExist', array(
  'fr' => 'Non determinée',
  'nl' => 'Niet bepaald',
  'en' => 'n/a',
  'mk' => 'не постои',
));
dict_add('StillActive', array(
  'fr' => 'Toujours actif',
  'nl' => 'Nog actief',
  'en' => 'Still active',
  'mk' => 'Сеушто активен',
));
dict_add('CalendarNotAvailable', array(
  'fr' => 'Aucun calendrier n\'est disponible pour les critère(s) choisi(s).',
  'nl' => 'Geen enkele kalender is beschikbaar voor de gekozen criteria.',
  'en' => 'There is no calendar available for the selected criteria',
  'mk' => 'Нема внесен календар за зададениот услов.',
));
dict_add('ChangeTo', array(
  'fr' => 'Changer pour',
  'nl' => 'Wijzigen in',
  'en' => 'Change to',
  'mk' => 'Промени во',
));
dict_add('Processing', array(
  'fr' => 'Travail en cours.  Veuillez patienter quelques instants...',
  'nl' => 'Bezig.  Gelieve te wachten...',
  'en' => 'Work in progress.  Please wait',
  'mk' => 'Работам. Ве молам причекајте...',
));
dict_add('BadUserOrPassword', array(
  'fr' => 'Votre identifiant ou votre mot de passe n\'est pas correct.  Veuillez recommencer l\'opération.',
  'nl' => 'Je gebruikersnaam of paswoord is ongeldig.  Gelieve te herbeginnen.',
  'en' => 'Your login and/or password is not correct.  Please try again.',
  'mk' => 'Вашето корисничко име и/или лозинка се погрешни. Ве молам обидете се повторно.',
));
dict_add('Username', array(
  'fr' => 'Identifiant',
  'nl' => 'Gebruikersnaam',
  'en' => 'User name',
  'mk' => 'Корисничко име',
));
dict_add('Password', array(
  'fr' => 'Mot de passe',
  'nl' => 'Paswoord',
  'en' => 'Password',
  'mk' => 'Лозинка',
));
dict_add('LoginNow', array(
  'fr' => 'Identification',
  'nl' => 'Identificatie',
  'en' => 'Login',
  'mk' => 'Најава',
));
dict_add('UserLoggedOut', array(
  'fr' => 'Vous êtes correctement déconnecté.',
  'nl' => 'U bent correct afgemeld.',
  'en' => 'You are correctly disconnected',
  'mk' => 'Успешно сте одјавени',
));
dict_add('User', array(
  'fr' => 'Utilisateur',
  'nl' => 'Gebruiker',
  'en' => 'User',
  'mk' => 'Корисник',
));
dict_add('Login', array(
  'fr' => 'Connexion',
  'nl' => 'Login',
  'en' => 'Login',
  'mk' => 'Најава',
));
dict_add('Logout', array(
  'fr' => 'Déconnexion',
  'nl' => 'Afmelden',
  'en' => 'Logout',
  'mk' => 'Одјава',
));
dict_add('Register', array(
  'fr' => 'Enregistrement',
  'nl' => 'Registratie',
  'en' => 'Register',
  'mk' => 'Регистрација',
));
dict_add('Preferences', array(
  'fr' => 'Préférences',
  'nl' => 'Voorkeuren',
  'en' => 'Preferences',
  'mk' => 'Параметри',
));
dict_add('UserPreferences', array(
  'fr' => 'Préférences Utilisateur',
  'nl' => 'Gebr. Voorkeuren',
  'en' => 'User Preferences',
  'mk' => 'Параметри за корисникот',
));
dict_add('ProvincePreferences', array(
  'fr' => 'Préférences Province',
  'nl' => 'Prov. voorkeuren',
  'en' => 'Province Preferences',
  'mk' => 'Параметри за областа',
));
dict_add('Details', array(
  'fr' => 'Feuille de match',
  'nl' => 'Wedstrijdblad',
  'en' => 'Details',
  'mk' => 'Детали',
));
dict_add('Yes', array(
  'fr' => 'Oui',
  'nl' => 'Ja',
  'en' => 'Yes',
  'mk' => 'Да',
));
dict_add('No', array(
  'fr' => 'Non',
  'nl' => 'Neen',
  'en' => 'No',
  'mk' => 'Не',
));
dict_add('ImpossibleScoreFor', array(
  'fr' => 'Score impossible pour le match ',
  'nl' => 'Ongeldige score voor de wedstrijd ',
  'en' => 'Impossible score for the match',
  'mk' => 'Нерегуларен резултат за меч',
));
dict_add('Modifiable', array(
  'fr' => 'Modifiable',
  'nl' => 'Gegevens inputten',
  'en' => 'Changeable',
  'mk' => 'Изменлив',
));
dict_add('NotModifiable', array(
  'fr' => 'Non Modifiable',
  'nl' => 'Einde input gegevens',
  'en' => 'Non changeable',
  'mk' => 'Не е изменлив',
));
dict_add('NoDetail', array(
  'fr' => 'Il n\'y a pas de détail disponible pour ce match.',
  'nl' => 'Geen detail beschikbaar voor deze wedstrijd.',
  'en' => 'There is no detail available for this match',
  'mk' => 'Нема достапни детали за овој меч',
));
dict_add('CreateDetails', array(
  'fr' => 'Créer des résultats détaillés pour ce match.',
  'nl' => 'Maken van detailresultaten voor deze wedstrijd.',
  'en' => 'Create detailed results for this match',
  'mk' => 'Креирај детални резултати за овој меч',
));
dict_add('DetailsDeleteConfirm', array(
  'fr' => 'Voulez-vous réellement effacer les résultats détaillés de ce match',
  'nl' => 'Wilt U werkelijk de detailsresultaten van deze wedstrijd verwijderen',
  'en' => 'Do you really want to delete detailed results for this match',
  'mk' => 'Дали навистина сакаш да ги исбришеш деталните резултати за овој меч',
));
dict_add('DetailsDeleted', array(
  'fr' => 'Les détails de ce match ont été effacés correctement.',
  'nl' => 'De detailresultaten werden correct verwijderd.',
  'en' => 'The details of the match have been correctly deleted.',
  'mk' => 'Деталите за овој меч се успешно избришани.',
));
dict_add('DetailsAdded', array(
  'fr' => 'Les détails de ce match ont été ajoutés correctement.',
  'nl' => 'De detailresultaten werden correct toegevoegd.',
  'en' => 'The details of the match have been correctly added.',
  'mk' => 'Деталите за овој меч се успешно додадени.',
));
dict_add('Player', array(
  'fr' => 'Joueur',
  'nl' => 'Speler',
  'en' => 'Player',
  'mk' => 'Играч',
));
dict_add('PlayerDeleteConfirm', array(
  'fr' => 'Voulez-vous réellement effacer ce joueur',
  'nl' => 'Wilt U werkelijk deze speler verwijderen',
  'en' => 'Do you really want to delete this player',
  'mk' => 'Дали навистина сакаш да го избришеш овој играч',
));
dict_add('PlayerAddConfirm', array(
  'fr' => 'Voulez-vous réellement ajouter un joueur',
  'nl' => 'Wilt U werkelijk een speler toevoegen',
  'en' => 'Do you really want to add a player',
  'mk' => 'Дали навистина сакаш да додадеш играч',
));
dict_add('Name', array(
  'fr' => 'Nom',
  'nl' => 'Naam',
  'en' => 'Name',
  'mk' => 'Име',
));
dict_add('GameScoreError', array(
  'fr' => 'Le score du set #SN# match #GN# est incorrect.<br>Modification annulée.',
  'nl' => 'De score van de set #SN# wedstrijd #GN# is ongeldig.<br>Wijziging geannuleerd.',
  'en' => 'The score of set #SN# game #GN# is invalid.<br>Modification cancelled.',
  'mk' => 'Резултатот за сетот #SN# од мечот #GN# е невалиден.<br>Измената е прекината.',
));
dict_add('GameScoreErrorSetOnly', array(
  'fr' => 'Le score du match #GN# est incorrect.<br>Modification annulée.',
  'nl' => 'De score van wedstrijd #GN# is ongeldig.<br>Wijziging geannuleerd.',
  'en' => 'The score of game #GN# is invalid.<br>Modification cancelled.',
  'mk' => 'Резултатот за мечот #GN# е невалиден.<br>Измената е прекината.',
));
dict_add('Place', array(
  'fr' => 'Place',
  'nl' => 'Plaats',
  'en' => 'Place',
  'mk' => 'Место',
));
dict_add('TeamName', array(
  'fr' => 'Nom équipe',
  'nl' => 'Ploegnaam',
  'en' => 'Team name',
  'mk' => 'Име на тимот',
));
dict_add('GamesPlayed', array(
  'fr' => 'RJ',
  'nl' => 'AW',
  'en' => 'MP',
  'mk' => 'НВ',
));
dict_add('GamesWon', array(
  'fr' => 'RG',
  'nl' => 'GW',
  'en' => 'MW',
  'mk' => 'НД',
));
dict_add('GamesLost', array(
  'fr' => 'RP',
  'nl' => 'VW',
  'en' => 'ML',
  'mk' => 'НИ',
));
dict_add('GamesDraw', array(
  'fr' => 'RN',
  'nl' => 'DW',
  'en' => 'MD',
  'mk' => 'НН',
));
dict_add('MatchsWon', array(
  'fr' => 'MG',
  'nl' => 'GM',
  'en' => 'GW',
  'mk' => 'МД',
));
dict_add('MatchsLost', array(
  'fr' => 'MP',
  'nl' => 'VM',
  'en' => 'GL',
  'mk' => 'МИ',
));
dict_add('SetsWon', array(
  'fr' => 'SG',
  'nl' => 'GS',
  'en' => 'SW',
  'mk' => 'СД',
));
dict_add('SetsLost', array(
  'fr' => 'SP',
  'nl' => 'VS',
  'en' => 'SL',
  'mk' => 'СИ',
));
dict_add('Points', array(
  'fr' => 'Points',
  'nl' => 'Punten',
  'en' => 'Points',
  'mk' => 'Поени',
));
dict_add('Sets', array(
  'fr' => 'Sets',
  'nl' => 'Sets',
  'en' => 'Sets',
  'mk' => 'Сетови',
));
dict_add('Set', array(
  'fr' => 'Set',
  'nl' => 'Set',
  'en' => 'Set',
  'mk' => 'Сет',
));
dict_add('ClassementLegend', array(
  'fr' => '<SMALL><b>Légende</b></SMALL>:<TABLE style=\'font-size:xx-small;width:100%;\'><TR><TD>RJ: rencontres joués.</TD><TD>MG: matches gagnés</TD></TR><TR><TD>RG: rencontres gagnées.</TD><TD>MP: matches perdus</TD></TR><TR><TD>RP: rencontres perdues.</TD><TD>SG: sets gagnés</TD></TR><TR><TD>RN: rencontres nulles.</TD><TD>SP: sets perdus</TD></TR></TABLE>',
  'nl' => '<SMALL><b>Legende</b></SMALL>:<TABLE style=\'font-size:xx-small;width:100%;\'><TR><TD>AW: Gespeelde ontmoetingen.</TD><TD>GM: Gewonnen wedstrijden.</TD></TR><TR><TD>GW: Gewonnen ontmoetingen.</TD><TD>VM: Verloren wedstrijden.</TD><TD>GS: Gewonnen sets.</TD></TR><TR><TD>DW: Ontmoetingen op gelijkspel.</TD><TD>VW: Verloren ontmoetingen.</TD><TD>VS: verloren sets.</TD></TR></TABLE>',
  'en' => '<SMALL><b>Legend</b></SMALL>:<TABLE style=\'font-size:xx-small;width:100%;\'><TR><TD>MP: matches played.</TD><TD>MW: matches won.</TD></TR><TR><TD>GW: games won.</TD><TD>ML: matches lost.</TD></TR><TR><TD>GL: games lost.</TD><TD>SG: sets won.</TD></TR><TR><TD>MN: draw matches.</TD><TD>SP: sets lost.</TD></TR></TABLE>',
  'mk' => '<SMALL><b>Легенда</b></SMALL>:<TABLE style=\'font-size:xx-small;width:100%;\'><TR><TD>И: вкупно играни натпревари.</TD><TD>НД: добиени натпревари.</TD></TR><TR><TD>МД: добиени мечеви.</TD><TD>НИ: изгубени натпревари.</TD></TR><TR><TD>МИ: изгубени мечеви.</TD><TD>СД: добиени сетови.</TD></TR><TR><TD>НН: нерешени натпревари.</TD><TD>СИ: изгубени сетови.</TD></TR></TABLE>',
));
dict_add('NoDivision', array(
  'fr' => 'Aucune division disponible pour la saison.',
  'nl' => 'Geen afdeling beschikbaar voor dit seizoen.',
  'en' => 'There is no division available for this season',
  'mk' => 'Нема дефинирани дивизии за оваа сезона',
));
dict_add('PleaseSelect', array(
  'fr' => 'Veuillez choisir',
  'nl' => 'Kies',
  'en' => 'Please choose',
  'mk' => 'Изберете',
));
dict_add('NotEnoughRights', array(
  'fr' => 'Vous n\'avez pas les droits suffisants pour exécuter cette opération.',
  'nl' => 'U heeft onvoldoende rechten voor deze operatie.',
  'en' => 'You don\'t have suffisant rights to execute this operation',
  'mk' => 'Немате доволно права за да ја извршите оваа операција',
));
dict_add('NoPlayers', array(
  'fr' => 'Il n\'y a aucun joueur répondant au critère choisi.',
  'nl' => 'Geen enkele speler beantwoordt aan deze criteria.',
  'en' => 'There is no player matching the criteria',
  'mk' => 'Нема играч кој ги задоволува бараните услови',
));
dict_add('FirstName', array(
  'fr' => 'Prénom',
  'nl' => 'Voornaam',
  'en' => 'First name',
  'mk' => 'Име',
));
dict_add('LastName', array(
  'fr' => 'Nom',
  'nl' => 'Naam',
  'en' => 'Last name',
  'mk' => 'Презиме',
));
dict_add('Email', array(
  'fr' => 'e-mail',
  'nl' => 'e-mail',
  'en' => 'e-mail',
  'mk' => 'e-mail',
));
dict_add('MultipleClub', array(
  'fr' => '[*Plusieurs Clubs*]',
  'nl' => '[*Meerdere Clubs*]',
  'en' => '[*Multiple Clubs*]',
  'mk' => '[*Повеќе клубови*]',
));
dict_add('ClassementShort', array(
  'fr' => 'Cl.',
  'nl' => 'Kl.',
  'en' => 'Rk.',
  'mk' => 'Кл.',
));
dict_add('MultipleClass', array(
  'fr' => '**',
  'nl' => '**',
  'en' => '**',
  'mk' => '**',
));
dict_add('PlayerDeleted', array(
  'fr' => 'Le joueur #NAME# a été correctement supprimé.',
  'nl' => 'De speler  #NAME# werd correct verwijderd.',
  'en' => 'Player #NAME# is correctly deleted.',
  'mk' => 'Играчот #NAME# е успешно избришан.',
));
dict_add('Count', array(
  'fr' => 'Nombre',
  'nl' => 'Aantal',
  'en' => 'Number',
  'mk' => 'Број',
));
dict_add('CountShort', array(
  'fr' => 'Nb.',
  'nl' => 'Atl.',
  'en' => 'Cnt.',
  'mk' => 'Бр.',
));
dict_add('Index', array(
  'fr' => 'Nb',
  'nl' => 'N°',
  'en' => 'Nb',
  'mk' => 'Ix',
));
dict_add('VictoriesShort', array(
  'fr' => 'Vict.',
  'nl' => 'Ov.',
  'en' => 'Wins',
  'mk' => 'Добиени',
));
dict_add('DefeatsShort', array(
  'fr' => 'Déf.',
  'nl' => 'Ver.',
  'en' => 'Losts',
  'mk' => 'Изгубени',
));
dict_add('WO', array(
  'fr' => 'W-O',
  'nl' => 'FF',
  'en' => 'W-O',
  'mk' => 'W-O',
));
dict_add('Filters', array(
  'fr' => 'Filtres',
  'nl' => 'Filters',
  'en' => 'Filter',
  'mk' => 'Филтри',
));
dict_add('OpponentTeam', array(
  'fr' => 'Equipe<br>adverse',
  'nl' => 'Tegen<br>partij',
  'en' => 'Away<br>team',
  'mk' => 'Гостин<br>тим',
));
dict_add('HomeTeamIndice', array(
  'fr' => 'Eq.',
  'nl' => 'Pl.',
  'en' => 'Team',
  'mk' => 'Тим',
));
dict_add('OpponentName', array(
  'fr' => 'Nom adversaire',
  'nl' => 'Naam tegenstander',
  'en' => 'Opponent name',
  'mk' => 'Име на противник',
));
dict_add('ScoreSet', array(
  'fr' => 'Sets',
  'nl' => 'Sets',
  'en' => 'Sets',
  'mk' => 'Сетови',
));
dict_add('ResultsOf', array(
  'fr' => 'Résultats de #NAME#',
  'nl' => 'Resultaten van #NAME#',
  'en' => 'Results of #NAME#',
  'mk' => 'Резултати на #NAME#',
));
dict_add('NoResults', array(
  'fr' => 'Il n\'y a pas de résultats disponibles.',
  'nl' => 'Geen resultaten beschikbaar.',
  'en' => 'There is no result available',
  'mk' => 'Нема достапни резултати',
));
dict_add('NoFines', array(
  'fr' => 'Aucune amende',
  'nl' => 'Geen boetes',
  'en' => 'No fine',
  'mk' => 'Нема казни',
));
dict_add('ScoreRepartitionSet', array(
  'fr' => 'Répartition des scores (Sets):',
  'nl' => 'Verdeling van de scores (Sets):',
  'en' => 'Distribution of scores (Sets):',
  'mk' => 'Распоред на резултати (Сетови):',
));
dict_add('ScoreRepartitionPoints', array(
  'fr' => 'Répartition des scores (Points):',
  'nl' => 'Verdeling van de scores (Punten):',
  'en' => 'Distribution of scores (Points):',
  'mk' => 'Распоред на резултати (Поени):',
));
dict_add('SummaryPerClassement', array(
  'fr' => 'Résumé par classement',
  'nl' => 'Samenvatting per klassement',
  'en' => 'Summary per ranking',
  'mk' => 'Резиме по ранг',
));
dict_add('LastSetResults', array(
  'fr' => 'Résultats à la belle:',
  'nl' => 'Resultaat in de belle:',
  'en' => 'Last set results:',
  'mk' => 'Краен резултат по сетови:',
));
dict_add('MaximumSetPlayed', array(
  'fr' => 'Belles jouées',
  'nl' => 'Gespeelde belles',
  'en' => 'Last set played',
  'mk' => 'Последен игран сет',
));
dict_add('MaximumSetWon', array(
  'fr' => 'Belles gagnées',
  'nl' => 'Gewonnen belles',
  'en' => 'Last set won',
  'mk' => 'Последен добиен сет',
));
dict_add('MaximumSetLost', array(
  'fr' => 'Belles perdues',
  'nl' => 'Verloren belles',
  'en' => 'Last set lost',
  'mk' => 'Последен изгубен сет',
));
dict_add('Summary', array(
  'fr' => 'R&eacute;sum&eacute;',
  'nl' => 'Samengevat',
  'en' => 'Summary',
  'mk' => 'Резиме',
));
dict_add('Percentage', array(
  'fr' => 'Pourcentage',
  'nl' => 'Percent',
  'en' => 'Percentage',
  'mk' => 'Процент',
));
dict_add('OpponentShort', array(
  'fr' => 'Adv.',
  'nl' => 'Teg.',
  'en' => 'Opp.',
  'mk' => 'Прот.',
));
dict_add('PercentageShort', array(
  'fr' => 'Pourc.',
  'nl' => 'Perc.',
  'en' => 'Perc.',
  'mk' => 'Проц.',
));
dict_add('AbsolutePoints', array(
  'fr' => 'Points absolus',
  'nl' => 'Absolute punten',
  'en' => 'Absolute points',
  'mk' => 'Апсолутни поени',
));
dict_add('RelativePoints', array(
  'fr' => 'Points relatifs',
  'nl' => 'Relatieve punten',
  'en' => 'Relative points',
  'mk' => 'Релативни поени',
));
dict_add('GameMinimumDifference', array(
  'fr' => 'Sets joués avec le plus petit écart:',
  'nl' => 'Gespeelde sets met het kleinste verschil:',
  'en' => 'Sets played with the minimum difference:',
  'mk' => 'Сетови играни со минимална разлика',
));
dict_add('MinimumDiffPlayed', array(
  'fr' => 'Joués',
  'nl' => 'Gespeeld',
  'en' => 'Played',
  'mk' => 'Играни',
));
dict_add('MinimumDiffWon', array(
  'fr' => 'Gagnés',
  'nl' => 'Gewonnen',
  'en' => 'Won',
  'mk' => 'Добиени',
));
dict_add('MinimumDiffLost', array(
  'fr' => 'Perdus',
  'nl' => 'Verloren',
  'en' => 'Losts',
  'mk' => 'Изгубени',
));
dict_add('Navigation', array(
  'fr' => 'Raccourci',
  'nl' => 'Snelkoppeling',
  'en' => 'Shortcut',
  'mk' => 'Навигација',
));
dict_add('NewShortcut', array(
  'fr' => 'Nouveau',
  'nl' => 'Nieuw',
  'en' => 'New',
  'mk' => 'Нов',
));
dict_add('Save', array(
  'fr' => 'Sauver',
  'nl' => 'Opslaan',
  'en' => 'Save',
  'mk' => 'Сними',
));
dict_add('SaveAndBack', array(
  'fr' => 'Sauver et retour',
  'nl' => 'Opslaan en terug',
  'en' => 'Save and back',
  'mk' => 'Сними и оди Назад',
));
dict_add('URLtoSave', array(
  'fr' => 'URL à sauvegarder',
  'nl' => 'Te bewaren URL',
  'en' => 'URL to save',
  'mk' => 'Линк за снимање',
));
dict_add('URLName', array(
  'fr' => 'Nom du raccourci',
  'nl' => 'Naam van de snelkoppeling',
  'en' => 'Shortcut name',
  'mk' => 'Ине на Линк',
));
dict_add('MyLink', array(
  'fr' => 'Mon raccourci',
  'nl' => 'Mijn snelkoppeling',
  'en' => 'My shortcut',
  'mk' => 'Мои линкови',
));
dict_add('Link', array(
  'fr' => 'Raccourci',
  'nl' => 'Snelkoppeling',
  'en' => 'Shortcut',
  'mk' => 'Линк',
));
dict_add('PersonalLinks', array(
  'fr' => 'Mes raccourcis',
  'nl' => 'Mijn snelkoppelingen',
  'en' => 'My shortcuts',
  'mk' => 'Лични линкови',
));
dict_add('URLSaveTitle', array(
  'fr' => 'Sauvegarde d\'un raccourci',
  'nl' => 'Een snelkoppeling opslaan',
  'en' => 'Save a shortcut',
  'mk' => 'Сними линк',
));
dict_add('HowToCreateALink', array(
  'fr' => 'Pour créer un raccourci vers une page que vous utilisez souvent<br>cliquez sur l\'option \'Sauver\' du menu de gauche.',
  'nl' => 'Om een snelkoppeling te maken naar een veelgebruikte pagina<br>klik op de optie \'Opslaan\' van de linkermenu.',
  'en' => 'To create a shortcut to a page that your are using regulary<br>click on option \'Save\' of the left menu.',
  'mk' => 'За креирање на линк до страницата која ја користите често<br>кликнете на опцијата \'Сними\' во левото мени.',
));
dict_add('Language', array(
  'fr' => 'Langue',
  'nl' => 'Taal',
  'en' => 'Language',
  'mk' => 'Јазик',
));
dict_add('FrenchLang', array(
  'fr' => 'Français',
  'nl' => 'Frans',
  'en' => 'French',
  'mk' => 'Француски',
));
dict_add('DutchLang', array(
  'fr' => 'Néerlandais',
  'nl' => 'Nederlands',
  'en' => 'Dutch',
  'mk' => 'Холандски',
));
dict_add('EnglishLang', array(
  'fr' => 'Anglais',
  'nl' => 'Engels',
  'en' => 'English',
  'mk' => 'Англиски',
));
dict_add('MacedonianLang', array(
  'fr' => 'Macédonien',
  'nl' => 'Macedoniaan',
  'en' => 'Macedonian',
  'mk' => 'Македонски',
));
dict_add('Next', array(
  'fr' => 'Suivant',
  'nl' => 'Volgende',
  'en' => 'Next',
  'mk' => 'Наредно',
));
dict_add('Prev', array(
  'fr' => 'Précédant',
  'nl' => 'Vorige',
  'en' => 'Previous',
  'mk' => 'Претходно',
));
dict_add('SelectStyle', array(
  'fr' => 'Choisissez votre style',
  'nl' => 'Kies je stijl',
  'en' => 'Select your interface style',
  'mk' => 'Одберете стил на интерфејс.',
));
dict_add('SelectClub', array(
  'fr' => 'Sélectionnez votre club par défaut',
  'nl' => 'Kies je favoriete club',
  'en' => 'Select your default club',
  'mk' => 'Одберете го вашиот клуб',
));
dict_add('SelectDivision', array(
  'fr' => 'Choisissez vos divisions par défaut',
  'nl' => 'Kies je favoriete reeks',
  'en' => 'Select your default divisions',
  'mk' => 'Одберете ја вашата Дивизија',
));
dict_add('OtherParameters', array(
  'fr' => 'Autres paramètres',
  'nl' => 'Andere parameters',
  'en' => 'Other parameters',
  'mk' => 'Останати параметри',
));
dict_add('InvalidIndice', array(
  'fr' => 'L\'indice #INDICE# n\'est pas valide.',
  'nl' => 'De index #INDICE# is ongeldig.',
  'en' => '#INDICE# is not a valid indice.',
  'mk' => '#INDICE# е невалиден индекс.',
));
dict_add('DeleteDivConfirm', array(
  'fr' => 'Voulez-vous réellement effacer la division',
  'nl' => 'Wilt U werkelijk die reeks verwijder',
  'en' => 'Do you really want to delete this division',
  'mk' => 'Дали навистина сакате да ја избришете дивизијата',
));
dict_add('AddDivConfirm', array(
  'fr' => 'Voulez-vous réellement ajouter une nouvelle division',
  'nl' => 'Wilt U werkelijk een nieuwe reeks toevoegen',
  'en' => 'Do you really want to add a new division',
  'mk' => 'Дали навистина сакате да додадете дивизија',
));
dict_add('AddClubConfirm', array(
  'fr' => 'Voulez-vous réellement ajouter un nouveau club',
  'nl' => 'Wilt U werkelijk een nieuwe club toevoegen',
  'en' => 'Do you really want to add a new club',
  'mk' => 'Дали навистина сакате да додадете нов клуб',
));
dict_add('DeleteClubConfirm', array(
  'fr' => 'Voulez-vous réellement effacer le club',
  'nl' => 'Wilt U werkelijk die club verwijderen',
  'en' => 'Do you really want to delete this club',
  'mk' => 'Дали навситина саката да го избришете клубот',
));
dict_add('FineDeleteConfirm', array(
  'fr' => 'Voulez-vous réellement effacer cette amende',
  'nl' => 'Wilt U werkelijk die boete verwijderen',
  'en' => 'Do you really want to delete this fine',
  'mk' => 'Дали навистина сакате да ја избришете казната',
));
dict_add('MatchType', array(
  'fr' => 'Type de matches',
  'nl' => 'Soort wedstrijd',
  'en' => 'Matches type',
  'mk' => 'Тип на меч',
));
dict_add('PersonalResultsShort', array(
  'fr' => 'Rés. Individuels',
  'nl' => 'Individuele resultaten',
  'en' => 'Personal Results',
  'mk' => 'Индивидуални резултати',
));
dict_add('ClassementPerLastSet', array(
  'fr' => 'Classement par belles',
  'nl' => 'Klassement per belle',
  'en' => 'Classement per last sets played',
  'mk' => 'Раанг по последни играни сетови',
));
dict_add('PlaceShort', array(
  'fr' => 'Pl.',
  'nl' => 'Pl.',
  'en' => 'Pl.',
  'mk' => 'Место',
));
dict_add('Personal', array(
  'fr' => 'Individuel',
  'nl' => 'Individueel',
  'en' => 'Personal',
  'mk' => 'Индивидуални',
));
dict_add('PerLastSet', array(
  'fr' => 'Par Belles',
  'nl' => 'Per belle',
  'en' => 'Par Last Set',
  'mk' => 'По последни сетови',
));
dict_add('PerPercentage', array(
  'fr' => 'Par Pourcentage',
  'nl' => 'Per percent',
  'en' => 'Par percentage',
  'mk' => 'По процент',
));
dict_add('PerRelativePoints', array(
  'fr' => 'Par Points Relatifs',
  'nl' => 'Per relatieve punten',
  'en' => 'Par relative points',
  'mk' => 'По релативни поени',
));
dict_add('PerAbsolutePoints', array(
  'fr' => 'Par Points Absolus',
  'nl' => 'Per absolute punten',
  'en' => 'Par absolute points',
  'mk' => 'По апсолутни поени',
));
dict_add('Latest', array(
  'fr' => 'Plus récent',
  'nl' => 'Meest Recent',
  'en' => 'Latest',
  'mk' => 'Последни',
));
dict_add('WithResult', array(
  'fr' => 'Avec résultats',
  'nl' => 'Met resultaten',
  'en' => 'With results',
  'mk' => 'Со резултати',
));
dict_add('PersonalResults', array(
  'fr' => 'Résultats Individuels',
  'nl' => 'Individuele resultaten',
  'en' => 'Personal Results',
  'mk' => 'Индивидуални резултати',
));
dict_add('MatchList', array(
  'fr' => 'Liste des matches',
  'nl' => 'Ontmoetingslijst',
  'en' => 'Match list',
  'mk' => 'Листа на мечеви',
));
dict_add('SelectMenuAction', array(
  'fr' => 'Veuillez choisir une action dans le menu de gauche.',
  'nl' => 'Gelieve één actie in de linkermenu te kiezen.',
  'en' => 'Please choose an action on the left menu.',
  'mk' => 'Ве молам изберете акција од левото мени.',
));
dict_add('PleaseSelectAnItem', array(
  'fr' => 'Veuillez sélectionner un élement.',
  'nl' => 'Gelieve één item te kiezen',
  'en' => 'Please select an item.',
  'mk' => 'Ве молам одберете ставка.',
));
dict_add('ShowWebSite', array(
  'fr' => 'Site Web',
  'nl' => 'Website',
  'en' => 'Web site',
  'mk' => 'Веб сајт',
));
dict_add('ShowMap', array(
  'fr' => 'Carte',
  'nl' => 'Plan',
  'en' => 'Map',
  'mk' => 'Мапа',
));
dict_add('NoSiteForThisClub', array(
  'fr' => 'Aucun site n\'est disponible pour ce club',
  'nl' => 'Er is geen website beschikbaar voor deze club',
  'en' => 'No available web site for this club',
  'mk' => 'Нема достапен веб сајт',
));
dict_add('WebSite', array(
  'fr' => 'Site',
  'nl' => 'Site',
  'en' => 'Site',
  'mk' => 'Веб сајт',
));
dict_add('Hommes', array(
  'fr' => 'Hommes',
  'nl' => 'Heren',
  'en' => 'Men',
  'mk' => 'Маѓ',
));
dict_add('Dames', array(
  'fr' => 'Dames',
  'nl' => 'Dames',
  'en' => 'Women',
  'mk' => 'Жена',
));
dict_add('Jeunes', array(
  'fr' => 'Jeunes',
  'nl' => 'Jeugd',
  'en' => 'Youth',
  'mk' => 'Јуниори',
));
dict_add('Adults', array(
  'fr' => 'Adultes',
  'nl' => 'Volwassenen',
  'en' => 'Adults',
  'mk' => 'Сениори',
));
dict_add('FooterLeft', array(
  'fr' => '#DATE#',
  'nl' => '#DATE#',
  'en' => '#DATE#',
  'mk' => '#DATE#',
));
dict_add('FooterRight', array(
  'fr' => 'Page #PAGE# sur #NB_PAGE#',
  'nl' => 'Pagina #PAGE# op #NB_PAGE#',
  'en' => 'Page #PAGE# on #NB_PAGE#',
  'mk' => 'Страница #PAGE# од вкупно #NB_PAGE#',
));
dict_add('ChClubText', array(
  'fr' => "Vous pouvez modifier le club par défaut pour la session courante.  Ce paramètre sera cependant perdu pour votre prochaine connection.  Si vous désirez conserver ces paramètres, veuillez vous <A class=\"Interclubs\" href=\"#REGISTER_URL#\">enregistrer</A> sur le site",
  'nl' => "U kan de club wijzigen voor de huidige sessie.  Deze parameter zal echter niet bewaard worden.  Indien u wenst dat de gekozen club bewaard wordt voor uw volgende bezoek, gelieve u te <A class=Interclubs href=\"#REGISTER_URL#\">registreren</A>",
  'en' => "You can modify the default club for this session.  However, the parameter will be lost for you next visit.  If you want the site to remember you default club, please <A class=Interclubs href=\"#REGISTER_URL#\">register</A>",
  'mk' => "Можете да го промените преддефинираниот клуб за оваа сезона. Сепак, параметарот нема да биде валиден при следната посета.  Ако сакате сајтот да го запамети вашиот преддефиниран клуб, ве молам да се <A class=Interclubs href=\"#REGISTER_URL#\">регистрирате</A>",
));
dict_add('DefaultClub', array(
  'fr' => 'Club par défaut',
  'nl' => 'Favoriete club',
  'en' => 'Default club',
  'mk' => 'Преддефиниран клуб',
));
dict_add('LatestResults', array(
  'fr' => 'Derniers Résultats',
  'nl' => 'Meest Recente resultaten',
  'en' => 'Latest Results',
  'mk' => 'Последни резултати',
));
dict_add('Links', array(
  'fr' => 'Raccourcis',
  'nl' => 'Shortcuts',
  'en' => 'Shortcuts',
  'mk' => 'Кратенки',
));
dict_add('TeamLetter', array(
  'fr' => 'Lettre Eq.',
  'nl' => 'Letter Ploeg',
  'en' => 'Letter Team',
  'mk' => 'Letter Team',
));
dict_add('Criterium', array(
  'fr' => 'Critérium',
  'nl' => 'Criterium',
  'en' => 'Criterium',
  'mk' => 'Критериум',
));
dict_add('ToSee', array(
  'fr' => '&Agrave; voir',
  'nl' => 'Te zien',
  'en' => 'Don\'t miss',
  'mk' => 'Не пропуштајте',
));
dict_add('Singles', array(
  'fr' => 'Simples',
  'nl' => 'Enkels',
  'en' => 'Singles',
  'mk' => 'Единечно',
));
dict_add('Doubles', array(
  'fr' => 'Doubles',
  'nl' => 'Dubbels',
  'en' => 'Doubles',
  'mk' => 'Парови',
));
dict_add('Double', array(
  'fr' => 'Double',
  'nl' => 'Dubbel',
  'en' => 'Double',
  'mk' => 'Пар',
));
dict_add('Veterans', array(
  'fr' => 'Vétérans',
  'nl' => 'Veteranen',
  'en' => 'Veterans',
  'mk' => 'Ветерани',
));
dict_add('Masters', array(
  'fr' => 'Masters',
  'nl' => 'Masters',
  'en' => 'Masters',
  'mk' => 'Мастерс',
));
dict_add('PrintCurrent', array(
  'fr' => 'Une division',
  'nl' => 'Een afdeling',
  'en' => 'One division',
  'mk' => 'Една дивизија',
));
dict_add('PrintAllResults', array(
  'fr' => 'Impression des résultats de la semaine',
  'nl' => 'Afdruk van de weekresultaten',
  'en' => 'Printing of the week results',
  'mk' => 'Печатење на неделни резултати',
));
dict_add('MatchListWithDetail', array(
  'fr' => 'Liste des matches avec détails',
  'nl' => 'Lijst ontmoetingen met details',
  'en' => 'List of matches with details',
  'mk' => 'Листа на мечеви со детали',
));
dict_add('NoMatchAvailable', array(
  'fr' => 'Il n\'y a aucun match qui correspond à ce critère',
  'nl' => 'Geen ontmoeting beantwoordt aan de vereiste(n)',
  'en' => 'There is no match available for this criteria',
  'mk' => 'Не е пронајден меч со бараните услови',
));
dict_add('Bye', array(
  'fr' => 'Bye',
  'nl' => 'Vrij',
  'en' => 'Bye',
  'mk' => 'Пријатно',
));
dict_add('UnknownTeam', array(
  'fr' => '- ???? -',
  'nl' => '- ???? -',
  'en' => '- ???? -',
  'mk' => '- ???? -',
));
dict_add('SelectMatchAClickDetails', array(
  'fr' => 'Sélectionnez un match et cliquez sur Feuille de match dans le menu de gauche',
  'nl' => 'Kies een ontmoeting en klik op Wedstrijdblad in de linkermenu',
  'en' => 'Select a match a click on Details in the left menu',
  'mk' => 'Изберете меч со клик на Детали од левото мени',
));
dict_add('CrossResults', array(
  'fr' => 'Croisés',
  'nl' => 'Gekruist',
  'en' => 'Cross',
  'mk' => 'Вкрестени',
));
dict_add('Match', array(
  'fr' => 'Match',
  'nl' => 'Wedstrijd',
  'en' => 'Match',
  'mk' => 'Меч',
));
dict_add('NoResultForTeam', array(
  'fr' => 'Il n\'y a aucun résultat disponible pour ce club.',
  'nl' => 'Er zijn geen resultaten beschikbaar voor deze club',
  'en' => 'There is no result available for this club',
  'mk' => 'Нема достапни резултати за овој клуб',
));
dict_add('Close', array(
  'fr' => 'Fermer',
  'nl' => 'Sluiten',
  'en' => 'Close',
  'mk' => 'Затвори',
));
dict_add('ClubToSearch', array(
  'fr' => 'Club à chercher',
  'nl' => 'Te zoeken club',
  'en' => 'Club to search',
  'mk' => 'Барај клуб',
));
dict_add('Search', array(
  'fr' => 'Rechercher',
  'nl' => 'Zoeken',
  'en' => 'Search',
  'mk' => 'Барај',
));
dict_add('Validate', array(
  'fr' => 'Valider',
  'nl' => 'Bevestigen',
  'en' => 'Validate',
  'mk' => 'Провери',
));
dict_add('SaveAndValidate', array(
  'fr' => 'Sauvegarder et valider',
  'nl' => 'Opslaan en bevestigen',
  'en' => 'Save and validate',
  'mk' => 'Сними и Провери',
));
dict_add('YES', array(
  'fr' => 'OUI',
  'nl' => 'JA',
  'en' => 'YES',
  'mk' => 'ДА',
));
dict_add('NO', array(
  'fr' => 'NON',
  'nl' => 'NEE',
  'en' => 'NO',
  'mk' => 'НЕ',
));
dict_add('SaveCredentials', array(
  'fr' => 'Sauvegarder votre identifiant/login',
  'nl' => 'Bewaar uw login/paswoord',
  'en' => 'Save your credentials',
  'mk' => 'Снимете го името и лозинката',
));
dict_add('PlayerValueInTeam', array(
  'fr' => 'Contribution dans l\'équipe',
  'nl' => 'Contributie in de ploeg',
  'en' => 'Contribution in the team',
  'mk' => 'Придонес во тимот',
));
dict_add('ScoreRepartition', array(
  'fr' => 'Répartition des scores',
  'nl' => 'Verdeling van de scores',
  'en' => 'Distribution of the scores',
  'mk' => 'Распоредување на резултати',
));
dict_add('Victory', array(
  'fr' => 'Victoire',
  'nl' => 'Overwinning',
  'en' => 'Win',
  'mk' => 'Добиена',
));
dict_add('Defeat', array(
  'fr' => 'Défaite',
  'nl' => 'Verlies',
  'en' => 'Lost',
  'mk' => 'Изгубена',
));
dict_add('Draw', array(
  'fr' => 'Nul',
  'nl' => 'Gelijkspel',
  'en' => 'Draw',
  'mk' => 'Нерешена',
));
dict_add('NoPictureAvailable', array(
  'fr' => 'Aucune photo disponible',
  'nl' => 'Geen foto beschikbaar',
  'en' => 'No picture available',
  'mk' => 'Сликата не е достапна',
));
dict_add('ViewPicture', array(
  'fr' => 'Voir la photo',
  'nl' => 'Foto zien',
  'en' => 'View the picture',
  'mk' => 'Прикажи ја сликата',
));
dict_add('Misc', array(
  'fr' => 'Divers',
  'nl' => 'Diversen',
  'en' => 'Misc',
  'mk' => 'Разно',
));
dict_add('Pictures', array(
  'fr' => 'Photos',
  'nl' => 'Foto\'s',
  'en' => 'Pictures',
  'mk' => 'Слики',
));
dict_add('Gallery', array(
  'fr' => 'Galerie',
  'nl' => 'Galerij',
  'en' => 'Gallery',
  'mk' => 'Галерија',
));
dict_add('NoTeamForClub', array(
  'fr' => 'Il n\'existe aucune équipe pour ce club',
  'nl' => 'Er is geen ploeg voor deze club',
  'en' => 'No team for that club',
  'mk' => 'Клубот нема тим',
));
dict_add('PerMatch', array(
  'fr' => 'Par match',
  'nl' => 'Per wedstrijd',
  'en' => 'Per match',
  'mk' => 'По меч',
));
dict_add('PointsPlayedPerMatch', array(
  'fr' => 'Points joués par match',
  'nl' => 'Gespeelde punten per match',
  'en' => 'Points played per match',
  'mk' => 'Играни поени по меч',
));
dict_add('Average', array(
  'fr' => 'Moyenne',
  'nl' => 'Gemiddelde',
  'en' => 'Average',
  'mk' => 'Просек',
));
dict_add('PerSet', array(
  'fr' => 'Par set',
  'nl' => 'Per set',
  'en' => 'Per set',
  'mk' => 'По сет',
));
dict_add('December', array(
  'fr' => 'Décembre',
  'nl' => 'December',
  'en' => 'December',
  'mk' => 'Декември',
));
dict_add('ShowWithdrawals', array(
  'fr' => 'Voir les abandons',
  'nl' => 'Toon forfait',
  'en' => 'Show withdrawals',
  'mk' => 'Прикажи предадени',
));
dict_add('AllYourResults', array(
  'fr' => 'Tous vos r&eacute;sultats',
  'nl' => 'Al uw resultaten',
  'en' => 'All your results',
  'mk' => 'Сите ваши резултати',
));
dict_add('Played', array(
  'fr' => 'Joués',
  'nl' => 'Gespeeld',
  'en' => 'Played',
  'mk' => 'Играни',
));
dict_add('Won', array(
  'fr' => 'Gagnés',
  'nl' => 'Gewonnen',
  'en' => 'Won',
  'mk' => 'Добиени',
));
dict_add('Lost', array(
  'fr' => 'Perdus',
  'nl' => 'Verloren',
  'en' => 'Lost',
  'mk' => 'Изгубени',
));
dict_add('TeamTotal', array(
  'fr' => 'Total de l\'équipe',
  'nl' => 'Totaal van de ploeg',
  'en' => 'Team summary',
  'mk' => 'Резиме за тимот',
));
dict_add('ThisWeek', array(
  'fr' => 'Cette semaine',
  'nl' => 'Deze week',
  'en' => 'This week',
  'mk' => 'Оваа недела',
));
dict_add('Top50', array(
  'fr' => 'Top 50',
  'nl' => 'Top 50',
  'en' => 'Top 50',
  'mk' => 'Топ 50',
));
dict_add('Top12', array(
  'fr' => 'Top 12',
  'nl' => 'Top 12',
  'en' => 'Top 12',
  'mk' => 'Топ 12',
));
dict_add('ViewTop50', array(
  'fr' => 'Voir la totalité du top 50',
  'nl' => 'De volledige top 50 zien',
  'en' => 'Show the whole top 50',
  'mk' => 'Прикажи ги сите од Топ 50',
));
dict_add('Top50Week', array(
  'fr' => '11',
  'nl' => '11',
  'en' => '11',
  'mk' => '11',
));
dict_add('Comments', array(
  'fr' => 'Commentaires sur le match',
  'nl' => 'Commentaar op de wedstrijd',
  'en' => 'Comments about the match',
  'mk' => 'Коментари за мечот',
));
dict_add('NoComment', array(
  'fr' => 'Aucun commentaire sur ce match',
  'nl' => 'Geen commentaar op deze wedstrijd',
  'en' => 'No comment about this match',
  'mk' => '',
));
dict_add('AddNewComment', array(
  'fr' => 'Ajouter un nouveau commentaire',
  'nl' => 'Voeg een nieuwe commentaar toe',
  'en' => 'Add a new comment',
  'mk' => 'Додади нов коментар',
));
dict_add('Comment', array(
  'fr' => 'Commentaire',
  'nl' => 'Commentaar',
  'en' => 'Comment',
  'mk' => 'Коментар',
));
dict_add('Continue', array(
  'fr' => 'Continuer',
  'nl' => 'Doorgaan',
  'en' => 'Continue',
  'mk' => 'Продолжи',
));
dict_add('ToRegister', array(
  'fr' => 'L\'opération que vous avez demandée ne vous est pas accessible.  Soit vous n\'êtes pas enregistré, soit vos droits ne sont pas suffisants pour cette partie du site.',
  'nl' => 'De gevraagde handeling is voor u niet beschikbaar.  Ofwel bent U niet geregistreerd, of u heeft onvoldoende rechten om naar dit gedeelte te gaan.',
  'en' => 'The requested operation is not allowed.  Either you are not registred, either you do not have enough rights to reach this part of the site.',
  'mk' => 'Забранета операција. Или не сте регистрирани, или немате доволно привилегии за да пристапите кон тој дел на сајтот.',
));
dict_add('Step', array(
  'fr' => '&Eacute;tape',
  'nl' => 'Stap',
  'en' => 'Step',
  'mk' => 'Чекор',
));
dict_add('BirthDate', array(
  'fr' => 'Date de naissance',
  'nl' => 'Geboortedatum',
  'en' => 'Birth date',
  'mk' => 'Роденден',
));
dict_add('WeekComments', array(
  'fr' => 'Commentaires sur les matches de la semaine',
  'nl' => 'Commentaar op de weekmatchen',
  'en' => 'Comments on the week matches',
  'mk' => 'Коментари за мечеви од неделата',
));
dict_add('ResultsInputExplanation', array(
  'fr' => 'Veuillez remplir le formulaire avec les scores des rencontres.<BR>Les équipes ayant déclaré forfait doivent être marquées en sélectionnant la case à côté de leur nom (appuyer sur Voir les w-o si vous ne voyez pas cette case).<BR>Lorsque la fiche est complète, valider celle-ci en appuyant sur ENTER ou en cliquant sur l\'icone en bas de la fiche.',
  'nl' => 'Gelieve de resultaten van de ontmoetingen in te vullen in het formulier.<BR>Voor de ploegen die forfait geven, vinkt u het aankruisvakje naast hun naam aan (vink het vakje Toon forfait aan indien de aankruisvakjes niet zichtbaar zijn).<BR>Wanneer de resultaten volledig zijn, dient u deze te bevestigen door op ENTER te drukken of op het ikoontje onder de fiche.',
  'en' => 'Please enter the results of the matches.<BR>You can mark the teams that have forfaited theirShow w-o).<BR>When you have completed the results, confirm by pressing ENTER of clicking the icon below.',
  'mk' => 'Ве молам внесете резултат за мечевите.<br>Кога ќе завршите со внесување, потврдете со кликање на Ентер или на иконата подолу.',
));
dict_add('FF', array(
  'fr' => 'ff',
  'nl' => 'ff',
  'en' => 'w',
  'mk' => 'FF',
));
dict_add('GW', array(
  'fr' => 'fg',
  'nl' => 'af',
  'en' => 'gw',
  'mk' => 'GW',
));
dict_add('SM', array(
  'fr' => 'sm',
  'nl' => 'gu',
  'en' => 'sm',
  'mk' => 'SM',
));
dict_add('ShowHideWO', array(
  'fr' => 'Voir les w-o',
  'nl' => 'Toon forfait',
  'en' => 'Show w-o',
  'mk' => 'Прикажи w-o',
));
dict_add('EvaluatingNewClassement', array(
  'fr' => 'Evaluation du nouveau classement',
  'nl' => 'Evaluatie van het nieuwe klassement',
  'en' => 'Evaluating the new classement',
  'mk' => 'Евалуација на ново рангирање',
));
dict_add('NewClassMethod', array(
  'fr' => 'Méthode',
  'nl' => 'Methode',
  'en' => 'Method',
  'mk' => 'Метода',
));
dict_add('NewClassement', array(
  'fr' => 'Nouveau<br>Classement',
  'nl' => 'Nieuwe<br>Klassement',
  'en' => 'New<br>Classement',
  'mk' => 'Ново<br>Рангирање',
));
dict_add('Compute', array(
  'fr' => 'Calculs',
  'nl' => 'Berekeningen',
  'en' => 'Compute',
  'mk' => 'Пресметај',
));
dict_add('Picture', array(
  'fr' => 'Photo',
  'nl' => 'Foto',
  'en' => 'Picture',
  'mk' => 'Слика',
));
dict_add('DefaultPicture', array(
  'fr' => 'Photo par défaut',
  'nl' => 'Favoriete foto',
  'en' => 'Default picture',
  'mk' => 'Стандардна слика',
));
dict_add('MatchDaysAndTime', array(
  'fr' => 'Jours et heures des rencontres',
  'nl' => 'Dagen en aanvangsuren van de wedstrijden',
  'en' => 'Days and time of the matches',
  'mk' => 'Датум и време на натпревари',
));
dict_add('Interclubs', array(
  'fr' => 'Interclubs',
  'nl' => 'Interclub',
  'en' => 'Interclubs',
  'mk' => 'Interclubs',
));
dict_add('Error', array(
  'fr' => 'Erreur',
  'nl' => 'Vergissing',
  'en' => 'Error',
  'mk' => 'Грешка',
));
dict_add('Sex', array(
  'fr' => 'Sexe',
  'nl' => 'Geslacht',
  'en' => 'Sex',
  'mk' => 'Пол',
));
dict_add('SexM', array(
  'fr' => 'Masculin',
  'nl' => 'Mannelijk',
  'en' => 'Man',
  'mk' => 'Машки',
));
dict_add('SexF', array(
  'fr' => 'Féminin',
  'nl' => 'Vrouwelijk',
  'en' => 'Woman',
  'mk' => 'Женски',
));
dict_add('Nationality', array(
  'fr' => 'Nationalité',
  'nl' => 'Nationaliteit',
  'en' => 'Nationality',
  'mk' => 'Националност',
));
dict_add('HomePhone', array(
  'fr' => 'Tél. Privé',
  'nl' => 'Tel. Thuis',
  'en' => 'Home phone',
  'mk' => 'Домашен телефон',
));
dict_add('OfficePhone', array(
  'fr' => 'Tél. Bureau',
  'nl' => 'Tel. Bureel',
  'en' => 'Office phone',
  'mk' => 'Службен телефон',
));
dict_add('Phone', array(
  'fr' => 'Téléphone',
  'nl' => 'Telefoon',
  'en' => 'Phone',
  'mk' => 'Телефон',
));
dict_add('Fax', array(
  'fr' => 'Fax',
  'nl' => 'Fax',
  'en' => 'Fax',
  'mk' => 'Факс',
));
dict_add('Gsm', array(
  'fr' => 'GSM',
  'nl' => 'GSM',
  'en' => 'Mobile',
  'mk' => 'Мобилен',
));
dict_add('MedicValidity', array(
  'fr' => 'Validité Cert. Médical',
  'nl' => 'Geldigheid Medisch attest',
  'en' => 'Validity of medical attest',
  'mk' => 'Важност на лекарскиот преглед',
));
dict_add('PlayerList', array(
  'fr' => 'Liste des forces',
  'nl' => 'Sterktelijst',
  'en' => 'Player List',
  'mk' => 'Листа на играчи',
));
dict_add('PlayerUniqueIndex', array(
  'fr' => 'Numéro d\'affiliation',
  'nl' => 'Lidnummer',
  'en' => 'Player Id',
  'mk' => 'Индекс на играчот',
));
dict_add('PlayerClubIndex', array(
  'fr' => 'Numéro ordre',
  'nl' => 'Volg.Nummer',
  'en' => 'Player club index',
  'mk' => 'Индекс на клубот на играчот',
));
dict_add('PlayerIndex', array(
  'fr' => 'Index',
  'nl' => 'Index',
  'en' => 'Index',
  'mk' => 'Индекс',
));
dict_add('PlayerVttlIndex', array(
  'fr' => 'VTTL Index',
  'nl' => 'VTTL Index',
  'en' => 'VTTL Index',
  'mk' => 'VTTL Индекс',
));
dict_add('PlayerUniqueIndexShort', array(
  'fr' => 'Id',
  'nl' => 'Lidnr',
  'en' => 'Id',
  'mk' => 'Id',
));
dict_add('VeteransDames', array(
  'fr' => 'Aînées',
  'nl' => 'Dames Veteranen',
  'en' => 'Vetarans Lady',
  'mk' => 'Vetarans Lady',
));
dict_add('Benjamins', array(
  'fr' => 'Benjamins',
  'nl' => 'Benjamins',
  'en' => 'Benjamin',
  'mk' => 'Benjamin',
));
dict_add('BenjaminsFilles', array(
  'fr' => 'Benjamines',
  'nl' => 'Benjamins meisjes',
  'en' => 'Benjamin Girl',
  'mk' => 'Benjamin Girl',
));
dict_add('Preminimes', array(
  'fr' => 'Préminimes',
  'nl' => 'Pre-miniemen',
  'en' => 'Preminim',
  'mk' => 'Preminim',
));
dict_add('PreminimesFilles', array(
  'fr' => 'Préminimes Filles',
  'nl' => 'Pre-miniemen meisjes',
  'en' => 'Preminim Girl',
  'mk' => 'Preminim Girl',
));
dict_add('Minimes', array(
  'fr' => 'Minimes',
  'nl' => 'Miniemen',
  'en' => 'Minim',
  'mk' => 'Minim',
));
dict_add('MinimesFilles', array(
  'fr' => 'Minimes Filles',
  'nl' => 'Miniemen meisjes',
  'en' => 'Minim Girl',
  'mk' => 'Minim Girl',
));
dict_add('Cadets', array(
  'fr' => 'Cadets',
  'nl' => 'Cadetten',
  'en' => 'Cadet',
  'mk' => 'Cadet',
));
dict_add('Cadettes', array(
  'fr' => 'Cadettes',
  'nl' => 'Cadetten meisjes',
  'en' => 'Cadet Girl',
  'mk' => 'Cadet Girl',
));
dict_add('Juniors', array(
  'fr' => 'Juniors',
  'nl' => 'Juniors',
  'en' => 'Junior',
  'mk' => 'Junior',
));
dict_add('Juniores', array(
  'fr' => 'Juniores',
  'nl' => 'Juniors meisjes',
  'en' => 'Junior Girl',
  'mk' => 'Junior Girl',
));
dict_add('JeugdPlus', array(
  'fr' => 'Jeunes +',
  'nl' => 'Jeugd +',
  'en' => 'Youth +',
  'mk' => 'Youth +',
));
dict_add('JeugdPlusF', array(
  'fr' => 'Jeunes + Filles',
  'nl' => 'Jeugd + meisjes',
  'en' => 'Youth + Girl',
  'mk' => 'Youth + Girl',
));
dict_add('J21', array(
  'fr' => 'Jeunes -21',
  'nl' => 'Jeugd -21',
  'en' => 'Youth -21',
  'mk' => 'Youth -21',
));
dict_add('J21_F', array(
  'fr' => 'Jeunes -21 Filles',
  'nl' => 'Jeugd -21 meisjes',
  'en' => 'Youth -21 Girl',
  'mk' => 'Youth -21 Girl',
));
dict_add('17_21', array(
  'fr' => '17-21',
  'nl' => '17-21',
  'en' => '17-21',
  'mk' => '17-21',
));
dict_add('17_21Filles', array(
  'fr' => '17-21 Filles',
  'nl' => '17-21 meisjes',
  'en' => '17-21 Girl',
  'mk' => '17-21 Girl',
));
dict_add('Veterans40', array(
  'fr' => 'Vétérans 40',
  'nl' => 'Veteranen 40',
  'en' => 'Veterans 40',
  'mk' => 'Veterans 40',
));
dict_add('Veterans50', array(
  'fr' => 'Vétérans 50',
  'nl' => 'Veteranen 50',
  'en' => 'Veterans 50',
  'mk' => 'Veterans 50',
));
dict_add('Veterans60', array(
  'fr' => 'Vétérans 60',
  'nl' => 'Veteranen 60',
  'en' => 'Veterans 60',
  'mk' => 'Veterans 60',
));
dict_add('Veterans65', array(
  'fr' => 'Vétérans 65',
  'nl' => 'Veteranen 65',
  'en' => 'Veterans 65',
  'mk' => 'Veterans 65',
));
dict_add('Veterans70', array(
  'fr' => 'Vétérans 70',
  'nl' => 'Veteranen 70',
  'en' => 'Veterans 70',
  'mk' => 'Veterans 70',
));
dict_add('Veterans75', array(
  'fr' => 'Vétérans 75',
  'nl' => 'Veteranen 75',
  'en' => 'Veterans 75',
  'mk' => 'Veterans 75',
));
dict_add('Veterans80', array(
  'fr' => 'Vétérans 80',
  'nl' => 'Veteranen 80',
  'en' => 'Veterans 80',
  'mk' => 'Veterans 80',
));
dict_add('Veterans85', array(
  'fr' => 'Vétérans 85',
  'nl' => 'Veteranen 85',
  'en' => 'Veterans 85',
  'mk' => 'Veterans 85',
));
dict_add('Veterans40Dames', array(
  'fr' => 'Aînées 40',
  'nl' => 'Veteranen Dames 40',
  'en' => 'Veterans 40 Lady',
  'mk' => 'Veterans 40 Lady',
));
dict_add('Veterans50Dames', array(
  'fr' => 'Aînées 50',
  'nl' => 'Veteranen Dames 50',
  'en' => 'Veterans 50 Lady',
  'mk' => 'Veterans 50 Lady',
));
dict_add('Veterans60Dames', array(
  'fr' => 'Aînées 60',
  'nl' => 'Veteranen Dames 60',
  'en' => 'Veterans 60 Lady',
  'mk' => 'Veterans 60 Lady',
));
dict_add('Veterans65Dames', array(
  'fr' => 'Aînées 65',
  'nl' => 'Veteranen Dames 65',
  'en' => 'Veterans 65 Lady',
  'mk' => 'Veterans 65 Lady',
));
dict_add('Veterans70Dames', array(
  'fr' => 'Aînées 70',
  'nl' => 'Veteranen Dames 70',
  'en' => 'Veterans 70 Lady',
  'mk' => 'Veterans 70 Lady',
));
dict_add('Veterans75Dames', array(
  'fr' => 'Aînées 75',
  'nl' => 'Veteranen Dames 75',
  'en' => 'Veterans 75 Lady',
  'mk' => 'Veterans 75 Lady',
));
dict_add('Veterans80Dames', array(
  'fr' => 'Aînées 80',
  'nl' => 'Veteranen Dames 80',
  'en' => 'Veterans 80 Lady',
  'mk' => 'Veterans 80 Lady',
));
dict_add('Veterans85Dames', array(
  'fr' => 'Aînées 85',
  'nl' => 'Veteranen Dames 85',
  'en' => 'Veterans 85 Lady',
  'mk' => 'Veterans 85 Lady',
));
dict_add('Strict', array(
  'fr' => 'Strict',
  'nl' => 'Strikt',
  'en' => 'Strict',
  'mk' => 'Строго',
));
dict_add('NumberPerPage', array(
  'fr' => 'Nombre d\'&eacute;l&eacute;ments par page',
  'nl' => 'Aantal items per pagina',
  'en' => 'Number of items per page',
  'mk' => 'Број на записи по страна',
));
dict_add('NumberDivisionPerPage', array(
  'fr' => 'Nombre de divisions par page (impression r&eacute;sultats)',
  'nl' => 'Aantal afdelingen per pagina (afdruk resultaten)',
  'en' => 'Number of division per page (results printing)',
  'mk' => 'Број на дивизии по страна (печатење на резултати)',
));
dict_add('RestrictClubListTo', array(
  'fr' => 'Restreindre la liste des clubs à',
  'nl' => 'Clublijst beperken tot',
  'en' => 'Limit club list to',
  'mk' => 'Лимитирај клуб листа на',
));
dict_add('SetsOnly', array(
  'fr' => 'Seulement les sets',
  'nl' => 'Alleen sets',
  'en' => 'Sets only',
  'mk' => 'Само сетови',
));
dict_add('NC', array(
  'fr' => 'NC',
  'nl' => 'NG',
  'en' => 'NC',
  'mk' => 'NC',
));
dict_add('News', array(
  'fr' => 'Dernières nouvelles',
  'nl' => 'Laatste nieuws',
  'en' => 'Latest news',
  'mk' => 'Последни вести',
));
dict_add('SuppressCopyright', array(
  'fr' => 'Supprimer le copyright à l\'impression',
  'nl' => 'Copyright niet weergeven op afdruk',
  'en' => 'Suppress copyright on print',
  'mk' => 'Сокриј авторски права при печатење',
));
dict_add('PlayerStatus', array(
  'fr' => 'Statut du joueur',
  'nl' => 'Statuut Speler',
  'en' => 'Player status',
  'mk' => 'Статус на играч',
));
dict_add('Active', array(
  'fr' => 'Actif',
  'nl' => 'Actief',
  'en' => 'Active',
  'mk' => 'Активен',
));
dict_add('NonActive', array(
  'fr' => 'Inactif',
  'nl' => 'Niet actief',
  'en' => 'Non active',
  'mk' => 'Неактивен',
));
dict_add('Recreant', array(
  'fr' => 'Récréant',
  'nl' => 'Recreant',
  'en' => 'Recreant',
  'mk' => 'Recreant',
));
dict_add('Recreatie', array(
  'fr' => 'Récréant',
  'nl' => 'Recreatie',
  'en' => 'Recreant',
  'mk' => 'Recreant',
));
dict_add('RecreantReserve', array(
  'fr' => 'Récréant Réserve',
  'nl' => 'Recreant Reserve',
  'en' => 'Recreant Reserve',
  'mk' => 'Recreant Reserve',
));
dict_add('SeniorHommes', array(
  'fr' => 'Seniors Hommes',
  'nl' => 'Seniors Heren',
  'en' => 'Seniors Men',
  'mk' => 'Сениори Мажи',
));
dict_add('SeniorDames', array(
  'fr' => 'Seniors Dames',
  'nl' => 'Seniors Dames',
  'en' => 'Seniors Women',
  'mk' => 'Сениори Жени',
));
dict_add('Delimiter', array(
  'fr' => 'Délimiteur de liste',
  'nl' => 'Scheidingsteken Lijst',
  'en' => 'List separator',
  'mk' => 'Сепаратор на листа',
));
dict_add('NonPlayingMembers', array(
  'fr' => 'Membre non joueur',
  'nl' => 'Niet spelend lid',
  'en' => 'Non playing member',
  'mk' => 'Ќленови кои не се натпреваруваат',
));
dict_add('Suspended', array(
  'fr' => 'Suspendu',
  'nl' => 'Geschorst',
  'en' => 'Suspended',
  'mk' => 'Суспенфирани',
));
dict_add('AdministrativeMembers', array(
  'fr' => 'Administratif',
  'nl' => 'Administratief',
  'en' => 'Administrative',
  'mk' => 'Администрација',
));
dict_add('DoubleAffiliation', array(
  'fr' => 'Double Affiliation',
  'nl' => 'Dubbele Aansluiting',
  'en' => 'Double Affiliation',
  'mk' => '',
));
dict_add('DoubleAffiliationSuper', array(
  'fr' => 'Double Affiliation (Super)',
  'nl' => 'Dubbele Aansluiting (Super)',
  'en' => 'Double Affiliation (Super)',
  'mk' => 'Двојна припаднот (Супер)',
));
dict_add('NonPlayingMembersShort', array(
  'fr' => 'Non joueur',
  'nl' => 'Niet spel.',
  'en' => 'Non playing',
  'mk' => 'Не игра',
));
dict_add('TableShort', array(
  'fr' => 'T.',
  'nl' => 'T.',
  'en' => 'T.',
  'mk' => 'Т.',
));
dict_add('TeamComposition', array(
  'fr' => 'Composition de l\'équipe',
  'nl' => 'Samenstelling Ploeg',
  'en' => 'Team composition',
  'mk' => 'Состав на тимот',
));
dict_add('DivisionShort', array(
  'fr' => 'Div.',
  'nl' => 'Afd.',
  'en' => 'Div.',
  'mk' => 'Див.',
));
dict_add('FromClub', array(
  'fr' => 'Par Club',
  'nl' => 'Per Club',
  'en' => 'Per Club',
  'mk' => 'По Клуб',
));
dict_add('Print', array(
  'fr' => 'Imprimer',
  'nl' => 'Afdrukken',
  'en' => 'Print',
  'mk' => '',
));
dict_add('From', array(
  'fr' => 'Du',
  'nl' => 'Vanaf',
  'en' => 'From',
  'mk' => 'Од',
));
dict_add('to', array(
  'fr' => 'au',
  'nl' => 'tot',
  'en' => 'to',
  'mk' => 'до',
));
dict_add('Tournaments', array(
  'fr' => 'Tournois',
  'nl' => 'Tornooien',
  'en' => 'Tournaments',
  'mk' => 'Турнири',
));
dict_add('Tournament', array(
  'fr' => 'Tournoi',
  'nl' => 'Tornooi',
  'en' => 'Tournament',
  'mk' => 'Турнир',
));
dict_add('TournamentShort', array(
  'fr' => 'Tourn.',
  'nl' => 'Torn.',
  'en' => 'Tourn.',
  'mk' => 'Турн.',
));
dict_add('NoTournament', array(
  'fr' => 'Il n\'existe actuellement aucun tournoi',
  'nl' => 'Er is tot nu toe geen tornooi',
  'en' => 'There is no tournament available',
  'mk' => 'Нема достапни турнири',
));
dict_add('Regional', array(
  'fr' => 'Régional',
  'nl' => 'Landelijk',
  'en' => 'Regional',
  'mk' => 'Регионален',
));
dict_add('Regional Wal.-Br.', array(
  'fr' => 'Régional Wal.-Br.',
  'nl' => 'Landelijk Wal.-Br.',
  'en' => 'Regional Wal.-Br.',
  'mk' => 'Регионален Wal.-Br.',
));
dict_add('Federal', array(
  'fr' => 'Fédéral',
  'nl' => 'Federaal',
  'en' => 'Federal',
  'mk' => 'Федерациски',
));
dict_add('National', array(
  'fr' => 'National',
  'nl' => 'Nationaal',
  'en' => 'National',
  'mk' => 'Национален',
));
dict_add('SuperDivision', array(
  'fr' => 'Super Division',
  'nl' => 'Super Afdeling',
  'en' => 'Super Division',
  'mk' => 'Супер Лига',
));
dict_add('External', array(
  'fr' => 'Externe',
  'nl' => 'Buiten',
  'en' => 'External',
  'mk' => 'Екстерен',
));
dict_add('FinesList', array(
  'fr' => 'Liste des amendes',
  'nl' => 'Boetelijst',
  'en' => 'Fine list',
  'mk' => 'Листа на казни',
));
dict_add('Fines', array(
  'fr' => 'Amendes',
  'nl' => 'Boetes',
  'en' => 'Fines',
  'mk' => 'Казни',
));
dict_add('Fine', array(
  'fr' => 'Amende',
  'nl' => 'Boete',
  'en' => 'Fine',
  'mk' => 'Казна',
));
dict_add('Amount', array(
  'fr' => 'Montant',
  'nl' => 'Bedrag',
  'en' => 'Amount',
  'mk' => 'Износ',
));
dict_add('IndirectFilters', array(
  'fr' => 'Filtres indirects',
  'nl' => 'Onrechtstreekse filters',
  'en' => 'Indirect filters',
  'mk' => 'Индиректни филтри',
));
dict_add('NoSeries', array(
  'fr' => 'Il n\'existe actuellement aucune série pour ce tournoi',
  'nl' => 'Er is tot nu toe geen reeks voor dit tornooi',
  'en' => 'There is no serie available for this tournament',
  'mk' => 'Нема достапна низа за овој турнир',
));
dict_add('MaximumShort', array(
  'fr' => 'Max.',
  'nl' => 'Max.',
  'en' => 'Max.',
  'mk' => 'Макс.',
));
dict_add('Maximum', array(
  'fr' => 'Maximum',
  'nl' => 'Maximum',
  'en' => 'Maximum',
  'mk' => 'Максимум',
));
dict_add('Minimum', array(
  'fr' => 'Minimum',
  'nl' => 'Minimum',
  'en' => 'Minimum',
  'mk' => 'Минимум',
));
dict_add('DateFrom', array(
  'fr' => 'Date début',
  'nl' => 'Begindatum',
  'en' => 'Start date',
  'mk' => 'Започнува на',
));
dict_add('DateTo', array(
  'fr' => 'Date fin',
  'nl' => 'Einddatum',
  'en' => 'End date',
  'mk' => 'Завршува на',
));
dict_add('AuthorisationNb', array(
  'fr' => 'Référence de l\'autorisation',
  'nl' => 'Toelatingsreferentie',
  'en' => 'Authorisation Reference',
  'mk' => 'Судско овластување',
));
dict_add('AuthorisationNbShort', array(
  'fr' => 'Réf.',
  'nl' => 'Ref.',
  'en' => 'Ref.',
  'mk' => 'Овл.',
));
dict_add('UrlRules', array(
  'fr' => 'URL règlement',
  'nl' => 'URL reglement',
  'en' => 'URL Rules',
  'mk' => 'Линк до Правила',
));
dict_add('Subscribings', array(
  'fr' => 'Inscriptions',
  'nl' => 'Inschrijvingen',
  'en' => 'Subscribings',
  'mk' => 'Пријави',
));
dict_add('Championship', array(
  'fr' => 'Championnat',
  'nl' => 'Kampioenschap',
  'en' => 'Championship',
  'mk' => 'Шампионат',
));
dict_add('ChampionshipShort', array(
  'fr' => 'Champ.',
  'nl' => 'Kamp.',
  'en' => 'Champ.',
  'mk' => 'Шамп.',
));
dict_add('SuggestedClassement', array(
  'fr' => 'Classement préconisé',
  'nl' => 'Voorstel klassering',
  'en' => 'Suggested classement',
  'mk' => 'Предложени класи',
));
dict_add('ClassementWeighting', array(
  'fr' => 'Pondération<br>Class.',
  'nl' => 'Gewichtigheid<br>Klass.',
  'en' => 'Class.<br>Weighting',
  'mk' => 'Класа.<br>Тежина',
));
dict_add('Top50Limit', array(
  'fr' => 'Pourc.Min.<br>Top 50',
  'nl' => 'Min.Perc.<br>Top 50',
  'en' => 'Min. Perc.<br>Top 50',
  'mk' => 'Мин. Проц.<br>Топ 50',
));
dict_add('NotEnoughResults', array(
  'fr' => 'Pas assez de résultats',
  'nl' => 'Onvoldoende resultaten',
  'en' => 'Not enough results',
  'mk' => 'Нема доволно резултати',
));
dict_add('Statistics', array(
  'fr' => 'Statistiques',
  'nl' => 'Statistieken',
  'en' => 'Statistics',
  'mk' => 'Статистика',
));
dict_add('MatchPlayerPerCategory', array(
  'fr' => 'Nombre de matches joués par catégorie',
  'nl' => 'Aantal gespeelde matchen per categorie',
  'en' => 'Count of played matches per category',
  'mk' => 'Број на изиграни натпревари по категорија',
));
dict_add('FirstRound', array(
  'fr' => '1er Tour',
  'nl' => '1ste Ronde',
  'en' => '1st Round',
  'mk' => '1ва Рунда',
));
dict_add('SecondRound', array(
  'fr' => '2ème Tour',
  'nl' => '2de Ronde',
  'en' => '2nd Round',
  'mk' => '2ра Рунда',
));
dict_add('PlayOff', array(
  'fr' => 'Play-Off',
  'nl' => 'Play-Off',
  'en' => 'Play-Off',
  'mk' => 'Play-Off',
));
dict_add('NoRecentNews', array(
  'fr' => 'Pas de nouvelles récentes',
  'nl' => 'Geen recent nieuws',
  'en' => 'No recent news',
  'mk' => 'Нема понови вести',
));
dict_add('by', array(
  'fr' => 'par',
  'nl' => 'door',
  'en' => 'by',
  'mk' => 'од',
));
dict_add('NewsDeleted', array(
  'fr' => 'La nouvelle a été correctement effacée.',
  'nl' => 'Het nieuws werd correct verwijderd.',
  'en' => 'The news has been correctly deleted.',
  'mk' => 'Веста е коректно избришана.',
));
dict_add('PlayerTeamSelection', array(
  'fr' => 'Nombre de séléctions dans l\'équipe',
  'nl' => 'Aantal selecties in de ploeg',
  'en' => 'Selection number in the team',
  'mk' => 'Selection number in the team',
));
dict_add('PlayerSelectionPerTeam', array(
  'fr' => 'Nombre de séléctions par équipe',
  'nl' => 'Aantal selecties per ploeg',
  'en' => 'Selection number per team',
  'mk' => 'Selection number per team',
));
dict_add('Selections', array(
  'fr' => 'Sélections',
  'nl' => 'Selecties',
  'en' => 'Selections',
  'mk' => 'Selections',
));
dict_add('MaxSelection', array(
  'fr' => 'Sélections max.',
  'nl' => 'Max. selecties',
  'en' => 'Max selections',
  'mk' => 'Max selections',
));
dict_add('ScoreModified', array(
  'fr' => 'Le score a été modifié par décision du conseil sportif.  Impossible de changer le score.',
  'nl' => 'De score werd door beslissing van sportieve raad gewijzigd.  Het is onmogelijk de score te veranderen.',
  'en' => 'Score has been modified per Sport Council decision.  It is impossible to change the score.',
  'mk' => 'Резултатот е изменет со одлука на ППФМ. Неможете да го измените овој резултат.',
));
dict_add('CommentNewClassMethod3', array(
  'fr' => 'Vu sa complexité, cette évaluation n\'est pas recalculée en temps réel.  La dernière mise à jour date du #LASTUPDATE#.',
  'nl' => 'Gezien de complexiteit van de evaluatie, wordt deze niet in reëele tijd opgebouwd.  De laatste verandering gebeurde op #LASTUPDATE#.',
  'en' => 'Given its complexity, this evaluation is not computed in real time.  Last update occured at #LASTUPDATE#',
  'mk' => 'Поради комплексноста, рангирањето не се извршува во реално време. Последно ажурирање е на #LASTUPDATE#',
));
dict_add('Total', array(
  'fr' => 'Total',
  'nl' => 'Totaal',
  'en' => 'Total',
  'mk' => 'Вкупно',
));
dict_add('NewClassementModified', array(
  'fr' => 'Classement modifié manuellement',
  'nl' => 'Klassement manueel gewijzigd',
  'en' => 'Classement manually changed',
  'mk' => 'Рангирањето е рачно изменето',
));
dict_add('ToValidate', array(
  'fr' => 'Cette division doit être approuvée',
  'nl' => 'De afdeling moet gevalideerd worden',
  'en' => 'This division must be approved',
  'mk' => 'Дивизијата мора да се одобри',
));
dict_add('ToUnvalidate', array(
  'fr' => "Pour annuler l\\'approbation de cette division",
  'nl' => 'Om de validatie van deze afdeling ongedaan te maken',
  'en' => 'To undo this division approval',
  'mk' => 'За прекин на барање на одобрување',
));
dict_add('NotValidated', array(
  'fr' => 'Attention: ces informations ne sont <b>pas</b> validées.',
  'nl' => 'Opgelet: deze gegevens werden nog <b>niet</b> gevalideerd!',
  'en' => 'Warning: this information has <b>not</b> been validated!',
  'mk' => 'Внимание: оваа информација <b>не</b> е потврдена!',
));
dict_add('NotValidatedShort', array(
  'fr' => 'Provisoire',
  'nl' => 'Voorlopig',
  'en' => 'Draft',
  'mk' => 'Нацрт',
));
dict_add('CannotModifyValidated', array(
  'fr' => 'Cette information étant validée, il est impossible de la modifier.',
  'nl' => 'Deze gegevens werd gevalideerd en kunnen dus niet gewijzigd worden.',
  'en' => 'This information is validated and cannot be modified.',
  'mk' => 'Информацијата е потврдена и неможе да се менува.',
));
dict_add('Reserve', array(
  'fr' => 'Réserve',
  'nl' => 'Reserve',
  'en' => 'Reserve',
  'mk' => 'Резерва',
));
dict_add('NoFine', array(
  'fr' => 'Aucune amende',
  'nl' => 'Geen boete',
  'en' => 'No fine',
  'mk' => 'Нема казни',
));
dict_add('FineCode', array(
  'fr' => 'Code',
  'nl' => 'Code',
  'en' => 'Code',
  'mk' => 'Код',
));
dict_add('TotalShort', array(
  'fr' => 'Tot.',
  'nl' => 'Tot.',
  'en' => 'Tot.',
  'mk' => 'Вк.',
));
dict_add('WeekStartOn', array(
  'fr' => 'La semaine commence le',
  'nl' => 'De week start op',
  'en' => 'Week start on',
  'mk' => 'Неделата започнува со',
));
dict_add('DeleteCalendarDates', array(
  'fr' => 'Supprimer les dates',
  'nl' => 'Datums verwijderen',
  'en' => 'Delete dates',
  'mk' => 'Избриши датуми',
));
dict_add('CreateCalendar', array(
  'fr' => 'Créer un calendrier',
  'nl' => 'Kalender creëren',
  'en' => 'Create calendar',
  'mk' => 'Креирај календар',
));
dict_add('DeleteCalendar', array(
  'fr' => 'Supprimer le calendrier',
  'nl' => 'Kalender verwijderen',
  'en' => 'Delete calendar',
  'mk' => 'Избриши календар',
));
dict_add('DatabaseError', array(
  'fr' => 'Une erreur de base de données est survenue.  L\'administrateur en a été notifié par e-mail.  Si l\'erreur persiste, n\'hésitez pas à le contacter en lui fournissant les informations suivantes :',
  'nl' => 'Een databasefout heeft zich voorgedaan.  De administrator word per e-mail verwitigd.  Indien het probleem zich blijft voordoen, gelieve deze te contacteren en hem volgende informatie over te maken:',
  'en' => ' A database error occured.  The administrator has been warned per e-mail.  If the error persists, please contact him directly with the following information:',
  'mk' => ' Грешка на базата. Администраторот е известен со email порака. Ако грешката продолжи да се повторува, контактирајте го со следнава информација:',
));
dict_add('CalendarDatesMissing', array(
  'fr' => 'Pas de dates disponibles pour ce calendrier cette saison',
  'nl' => 'Geen datums voor deze kalender en dit seizoen',
  'en' => 'No dates for this calendar this season',
  'mk' => 'Нема датуми за овој календар во оваа сезона',
));
dict_add('DivisionSubscribe', array(
  'fr' => 'Recevoir les notifications pour cette division',
  'nl' => 'Resultaten van deze afdeling per e-mail ontvangen',
  'en' => 'Subscribe to this division',
  'mk' => 'Претплати се на на оваа дивизија',
));
dict_add('DivisionUnsubscribe', array(
  'fr' => 'Ne plus recevoir les notifications pour cette division (actuellement envoyée à [%s].',
  'nl' => 'Geen resultaten van deze afdeling meer per e-mail ontvangen (wordt nu naar [%s] gestuurd)',
  'en' => 'Unsubscribe from this division (currently sent to [%s])',
  'mk' => 'Прекини ја претплатата на оваа дивизија (моментално се испраќа на [%s])',
));
dict_add('CalendarRoster', array(
  'fr' => 'Grille',
  'nl' => 'Rooster',
  'en' => 'Roster',
  'mk' => 'Распоред',
));
dict_add('Youth', array(
  'fr' => 'Jeunes',
  'nl' => 'Jeugd',
  'en' => 'Youth',
  'mk' => 'Youth',
));
dict_add('PlayersSummaryDescription', array(
  'fr' => '<b>Nombre</b> : nombre total de membres inscrit à la fédération<br><b>Actif</b> : nombre de membres repris sur la liste des forces en tant que joueurs efectifs<br><b>Réserve</b> : nombre de membres repris sur la liste des forces en tant qe réserves<br><b>Récréant</b> : nombre de membres non repris sur liste des forces<br><b>Non joueur</b> : nombre de membres ne jouant pas au tennis de table<br><b>Dames pas sur liste hommes</b> : Nombre de dames non reprises sur la liste des forces Hommes<br><b>Jeunes</b> : nombre de members préminimes, minimes ou cadets<br><b>Inactif</b> : nombre de joueurs n\'étant plus affiliés<br><b>Total</b> : total des joueurs ayant été affiliés au club récemment',
  'nl' => '<b>Aantal</b> : Totaal aantal leden ingeschreven bij de federatie<br><b>Actief</b> : aantal leden op de sterktelijsten als actieve spelers<br><b>Reserve</b> : aantal spelers op de sterktelijsten als recreant-reserve<br><b>Recreant</b> : aantal leden niet op de sterkenlijsten<br><b>Niet speler</b> : aantal leden die geen tafeltennis spelen<br><b>Dames niet op lijst heren</b> : aantal dames die niet in de sterktelijst Heren opgenomen worden<br><b>Jeugd</b> : aantal jeugdspelers pre-miniem, miniem of cadet<br><b>Niet actief</b> : aantal leden die niet meer aangesloten zijn<br><b>Totaal</b> : totaal aantal spelers recent aangesloten bij de club',
  'en' => 'Number : Total number of members affiliated to the federation<br>Active : number of members on the player lists as active players<br>Reserve : number of members on the player lists as  reserves<br>Recreant : number of members who dont appear on the player lists<br>Non player: number of members who dont play table tennis<br>Youngsters : number of members preminimes, minimes or cadets<br>Inactive : number of members who are no longer affiliated<br>Total : total of players who recently affiliated to the club',
  'mk' => 'Број : Број на членови поврзани со федерацијата<br>Активни : Број на членови на листата на играчи како активни играчи<br>Резерви : Број на членови на листата на играчи како резерви<br>Рекреативцки : број на членови кои ги нема на листата на играчи<br>Неиграчи: Број на членови кои не играат пинг-понг<br>Млади : Број на младинци и кадети<br>Неактивни : Број на членови кои повеќе не се поврзани<br>Вкупно : Вкупно играчи кои неодамна се поврзале кон клуб',
));
dict_add('WomanOnMenPlayerList', array(
  'fr' => 'Sur la liste hommes ?',
  'nl' => 'Op de herensterktelijst ?',
  'en' => 'On men player list ?',
  'mk' => 'На листа на играчи мажи ?',
));
dict_add('WomanOnMenPlayerListShort', array(
  'fr' => '<small>Dames pas<br>liste Hommes</small>',
  'nl' => '<small>Dames niet<br>op herenlijst</small>',
  'en' => '<small>Woman not<br>on men list</small>',
  'mk' => '<small>Жени не се <br>ма листа на мажи</small>',
));
dict_add('ClassementPyramid', array(
  'fr' => 'Pyramide des classements',
  'nl' => 'Klassementspiramide',
  'en' => 'Classement pyramid',
  'mk' => 'Пирамида на рангирање',
));
dict_add('IsYouthDivision', array(
  'fr' => 'Division &quot;Jeunes&quot;',
  'nl' => 'Afdeling &quot;Jeugd&quot;',
  'en' => '&quot;Youth&quot; division',
  'mk' => '&quot;Youth&quot; division',
));
dict_add('PlayerName', array(
  'fr' => 'Nom du joueur',
  'nl' => 'Naam van de speler',
  'en' => 'Player name',
  'mk' => 'Име на играч',
));
dict_add('HeadToHead', array(
  'fr' => 'Tête à tête',
  'nl' => 'Head to head',
  'en' => 'Head to head',
  'mk' => 'Head to Head',
));
dict_add('CannotEditResults', array(
  'fr' => 'Vous ne pouvez pas éditer les résultats.  Pour cela, il faut avoir le droit \'Administrateur\', \'Province\' ou \'Club\'.  Avec les droits \'Province\' et \'Club\', vous ne pouvez éditer que les résultats relatifs à votre province ou club.',
  'nl' => 'U mag deze uitslagen niet wijzigen.  U moet de recht \'Administrator\', \'Provincie\' of \'Club\' hebben.  Met de rechten \'Provincie\' en \'Club\' mag U alleen maar uitslagen van uw eigen provincie of club wijzigen.',
  'en' => ' You cannot edit those results.  You have to have \'Administrator\', \'Province\' or \'Club\' right.  With rights \'Province\' or \'Club\', you may only change results related to their own province or club.',
  'mk' => ' You cannot edit those results.  You have to have \'Administrator\', \'Province\' or \'Club\' right.  With rights \'Province\' or \'Club\', you may only change results related to their own province or club.',
));
dict_add('ResultsCard', array(
  'fr' => 'Fiche',
  'nl' => 'Steekkaart',
  'en' => 'Results Card',
  'mk' => 'Картичка со резултати',
));
dict_add('ResultsLastModified', array(
  'fr' => 'Dernière modification par #NAME# à #MODIFIED#.  Voir le <a class=\'Interclubs_Bottom\' href=\'#LINK#\'>journal des modifications</a>.',
  'nl' => 'Laatste wijziging door #NAME# op #MODIFIED#.  Zie <a class=\'Interclubs_Bottom\' href=\'#LINK#\'>volledige log</a> voor details.',
  'en' => 'Last modification by #NAME# on #MODIFIED#.  See <a class=\'Interclubs_Bottom\' href=\'#LINK#\'>full log</a> for details.',
  'mk' => 'Последна измена од #NAME# на #MODIFIED#. Погледај <a class=\'Interclubs_Bottom\' href=\'#LINK#\'>целосен лог</a> за детали.',
));
dict_add('DivisionModificationList', array(
  'fr' => 'Liste des modifications :',
  'nl' => 'Lijst van de wijzigingen :',
  'en' => 'List of modifications :',
  'mk' => 'Листа на измени',
));
dict_add('ScoreOfMatchModified', array(
  'fr' => 'Score modifié à #DATA#',
  'nl' => 'Score gewijzigd naar #DATA#',
  'en' => ' Score modified to #DATA#',
  'mk' => 'Резултатот е изменет во #DATA#',
));
dict_add('AddHomeTeamWO', array(
  'fr' => 'Ajout du forfait de l\'équipe visitée',
  'nl' => 'Toevoeging van forfait voor thuisploeg',
  'en' => 'Added W-O for home team',
  'mk' => 'Додаден W-O за домашниот тим',
));
dict_add('RemoveHomeTeamWO', array(
  'fr' => 'Suppression du forfait de l\'équipe visitée',
  'nl' => 'Schrappen van forfait voor thuisploeg',
  'en' => 'Removed W-O for home team',
  'mk' => 'Отстранет W-O за домашниот тим',
));
dict_add('AddAwayTeamWO', array(
  'fr' => 'Ajout du forfait de l\'équipe visiteuse',
  'nl' => 'Toevoeging van forfait voor bezoekende ploeg',
  'en' => 'Added W-O for away team',
  'mk' => 'Додаден W-O за гостинскиот тим',
));
dict_add('RemoveAwayTeamWO', array(
  'fr' => 'Suppression du forfait de l\'équipe visiteuse',
  'nl' => 'Schrappen van forfait voor bezoekende ploeg',
  'en' => 'Removed W-O for away team',
  'mk' => 'Отстранет W-O за гостинскиот тим',
));
dict_add('LastModificationTime', array(
  'fr' => 'Dernière modification le',
  'nl' => 'Laatste wijziging op',
  'en' => 'Last modification on',
  'mk' => 'Последна измена на',
));
dict_add('ModificationBy', array(
  'fr' => 'Modifié par',
  'nl' => 'Gewijzigd door',
  'en' => 'Modified by',
  'mk' => 'Изменето од',
));
dict_add('Modification', array(
  'fr' => 'Description de la modification',
  'nl' => 'Beschrijving van de wijziging ',
  'en' => 'Modification description',
  'mk' => 'Опис на измена',
));
dict_add('AddScoreModified', array(
  'fr' => 'Ajout score modifié',
  'nl' => 'Toevoegen gewijzigde score',
  'en' => 'Added score modified',
  'mk' => 'Додадена измена на резултат',
));
dict_add('RemoveScoreModified', array(
  'fr' => 'Suppression score modifié',
  'nl' => 'Schrappen gewijzigde score',
  'en' => 'Removed score modified',
  'mk' => 'Отстранета измена на резултат',
));
dict_add('DetailedScoreCreated', array(
  'fr' => 'Création des résultats détaillés',
  'nl' => 'Creatie van de gedetailleerde uitslagen',
  'en' => 'Detailed results created',
  'mk' => 'Креиран е детален резултат',
));
dict_add('DetailedScoreModified', array(
  'fr' => 'Modification des résultats détaillés',
  'nl' => 'Wijziging van de gedetailleerde uitslagen',
  'en' => 'Detailed results modified',
  'mk' => 'Деталниот резултат е изменет',
));
dict_add('DetailedScoreDeleted', array(
  'fr' => 'Suppression des résultats détaillés',
  'nl' => 'Afschaffing van de gedetailleerde uitslagen',
  'en' => 'Detailed results deleted',
  'mk' => 'Детаилниот резултат е избришан',
));
dict_add('MatchCommentDeleted', array(
  'fr' => 'Suppression d\'un commentaire',
  'nl' => 'Afschaffing van een commentaar',
  'en' => 'Comment deleted',
  'mk' => 'Коментарот е избришан',
));
dict_add('ScoreValidated', array(
  'fr' => 'Validation du résultat',
  'nl' => 'Bevestiging van de uitslag',
  'en' => 'Result validation',
  'mk' => 'Потврда на резултат',
));
dict_add('DivisionCreated', array(
  'fr' => 'Création de la division',
  'nl' => 'Creatie van de afdeling',
  'en' => 'Division creation',
  'mk' => 'Креирање на дивизија',
));
dict_add('LegalNotice', array(
  'fr' => 'Mention légale',
  'nl' => 'Wettelijk bericht',
  'en' => 'Legal notice',
  'mk' => 'Правни белешки',
));
dict_add('NewClassementSpecialCases', array(
  'fr' => 'Cas spéciaux',
  'nl' => 'Speciale gevallen',
  'en' => 'Special cases',
  'mk' => 'Специјали случаи',
));
dict_add('Send', array(
  'fr' => 'Envoyer',
  'nl' => 'Zenden',
  'en' => 'Upload',
  'mk' => 'Прикачи',
));
dict_add('TabTDataExchange', array(
  'fr' => 'Échange de données TabT',
  'nl' => 'TabT gegevensuitwisseling',
  'en' => 'TabT data exchange',
  'mk' => 'TabT размена на податоци',
));
dict_add('LostPassword', array(
  'fr' => 'Identifiant ou mot de passe perdu ?',
  'nl' => 'Gebruikersnaam of paswoord vergeten ?',
  'en' => 'Lost login or password ?',
  'mk' => 'Изгубено корисничко име или лозинка?',
));
dict_add('SearchDescription', array(
  'fr' => 'Tapez le nom ou le numéro d\'affiliation du joueur à rechercher et appuyez sur [ENTRÉE]',
  'nl' => 'Typ de naam of het aansluitingsnummer van de te zoeken speler en druk op [ENTER]',
  'en' => 'Type name or number of player to search and press [ENTER]',
  'mk' => 'Внесете име или број на играч за пребарување и притиснете [Enter]',
));
dict_add('Round', array(
  'fr' => 'Tour',
  'nl' => 'Ronde',
  'en' => 'Round',
  'mk' => 'Рунда',
));
dict_add('Rounds', array(
  'fr' => 'Tours',
  'nl' => 'Ronden',
  'en' => 'Rounds',
  'mk' => 'Рунди',
));
dict_add('LocalCount', array(
  'fr' => 'Nb Local',
  'nl' => 'Atl Lokalen',
  'en' => 'Local Count',
  'mk' => 'Локален бројач',
));
dict_add('ConfigureFineLists', array(
  'fr' => 'Configurer Listes amendes',
  'nl' => 'Boeteslijsten Configuren',
  'en' => 'Configure fine lists',
  'mk' => 'Конфигурирање на листи на казни',
));
dict_add('PrivilegdeUsers', array(
  'fr' => 'Utilisateurs privilégiers',
  'nl' => 'Bevoorrechte Gebruikers',
  'en' => 'Privileged Users',
  'mk' => 'Привилегирани корисници',
));
dict_add('UserManagerConsole', array(
  'fr' => 'Gestion des utilisateurs',
  'nl' => 'Gebruikersadministratie',
  'en' => 'User management',
  'mk' => 'Управување со корисници',
));
dict_add('Help', array(
  'fr' => 'Aide',
  'nl' => 'Hulp',
  'en' => 'Help',
  'mk' => 'Помош',
));
dict_add('PreferenceDoesNotExists', array(
  'fr' => 'La préférence [#PREFNAME#] n\'existe pas, veuillez contacter l\'administrateur',
  'nl' => 'De voorkeur [#PREFNAME#] bestaat niet, gelieve de administrateur te verwittigen',
  'en' => 'Preference [#PREFNAME#] does not exist, please contact the administrator',
  'mk' => 'Изборот [#PREFNAME#] не постои, ве молам контактирајте го администраторот',
));
dict_add('PDFLoading', array(
  'fr' => '<h1>Chargement du fichier PDF</h1><p><b>Veuillez patientez svp...</b></p>',
  'nl' => '<h1>Lading van het PDF bestand</h1><p><b>Gelieve te wachten aub...</b></p>',
  'en' => '<h1>Loading PDF file...</h1><p><b>Please wait...</b></p>',
  'mk' => '<h1>Вчитување на PDF датотека...</h1><p><b>ве молам причекајте...</b></p>',
));
dict_add('PDFLoaded', array(
  'fr' => '<h1>Fichier PDF chargé</h1><p>Le fichier PDF a été chargé correctement sur votre machine.  Votre navigateur va maintenant exécuter l\'action associée aux fichiers PDF.<br><br>Si le document n\'apparaît pas, cliquez <a href=#URL#>ici</a>.</p>',
  'nl' => '<h1>PDF bestand geladen</h1><p>Het PDF-bestand wordt op uw computer correct geladen.  Uw browser zal nu de actie die aan PDF-bestanden geassocieerd is uitvoeren.<br><br>Indien het document niet moet verschijnen, gelieve <a href=#URL#>hier</a> te klikken.</p>',
  'en' => '<h1>PDF file loaded</h1><p>PDF-file was successfully loaded on your computer. Your browser will now launch action associated to PDF files.<br><br>If the document does not show up',
  'mk' => '<h1>PDF датотеката е вчитана.</h1><p>PDF датотеката е успешно вчутана на вашиот компјутер. Вашиот броузер ќе ја започне акцијата поврзана со PDF датотеките.<br><br>Ако документот не се прикаже',
));
dict_add('PopupClosing', array(
  'fr' => '<p>Ce message disparaîtra dans #SEC# seconde(s).</p>',
  'nl' => '<p>Deze boodschap zal binnen #SEC# second(en) verdwijnen.</p>',
  'en' => '<p>This message will disappear in #SEC# second(s)</p>',
  'mk' => '<p>Оваа порака ќе исчезно во #SEC# секунди</p>',
));
dict_add('ScoreNotValidated', array(
  'fr' => 'Ce score n\\\'a pas été validé par un administrateur',
  'nl' => 'Deze score is nog niet door de interclubleider bevestigd',
  'en' => 'This score has not been validated by an administrator',
  'mk' => 'Овој резултат не е потврден од администраторот',
));
dict_add('ScoreNotValidatedByOtherTeam', array(
  'fr' => 'Ce score n\\\'a pas été validé par l\\\'équipe adverse',
  'nl' => 'Deze score is nog niet door de tegenstander bevestigd',
  'en' => 'This score has not been validated by the opponent',
  'mk' => 'Овој резултат не е потврден од противникот',
));
dict_add('Admin', array(
  'fr' => 'Admin',
  'nl' => 'Admin',
  'en' => 'Admin',
  'mk' => 'Админ',
));
dict_add('HommesShort', array(
  'fr' => 'HOM',
  'nl' => 'HER',
  'en' => 'MEN',
  'mk' => 'МАЖ',
));
dict_add('DamesShort', array(
  'fr' => 'DAM',
  'nl' => 'DAM',
  'en' => 'WOM',
  'mk' => 'ЖЕН',
));
dict_add('VeteransShort', array(
  'fr' => 'VETH',
  'nl' => 'VETH',
  'en' => 'VETH',
  'mk' => 'ВЕТ',
));
dict_add('VeteransDamesShort', array(
  'fr' => 'VETD',
  'nl' => 'VETD',
  'en' => 'VETW',
  'mk' => 'ВЕТЖ',
));
dict_add('PreminimesShort', array(
  'fr' => 'PREMG',
  'nl' => 'PREMJ',
  'en' => 'PREMB',
  'mk' => 'PREMB',
));
dict_add('PreminimesFillesShort', array(
  'fr' => 'PREMF',
  'nl' => 'PREMM',
  'en' => 'PREMG',
  'mk' => '',
));
dict_add('MinimesShort', array(
  'fr' => 'MING',
  'nl' => 'MINJ',
  'en' => 'MINB',
  'mk' => 'PREMG',
));
dict_add('MinimesFillesShort', array(
  'fr' => 'MINF',
  'nl' => 'MINM',
  'en' => 'MING',
  'mk' => 'MING',
));
dict_add('CadetsShort', array(
  'fr' => 'CADG',
  'nl' => 'CADJ',
  'en' => 'CADB',
  'mk' => 'CADB',
));
dict_add('CadettesShort', array(
  'fr' => 'CADF',
  'nl' => 'CADF',
  'en' => 'CADG',
  'mk' => 'CADG',
));
dict_add('JuniorsShort', array(
  'fr' => 'JUNG',
  'nl' => 'JUNJ',
  'en' => 'JUNB',
  'mk' => 'JUNB',
));
dict_add('JunioresShort', array(
  'fr' => 'JUNF',
  'nl' => 'JUNM',
  'en' => 'JUNG',
  'mk' => 'JUNG',
));
dict_add('17_21Short', array(
  'fr' => '1721G',
  'nl' => '1721J',
  'en' => '1721B',
  'mk' => '1721B',
));
dict_add('17_21FillesShort', array(
  'fr' => '1721F',
  'nl' => '1721M',
  'en' => '1721G',
  'mk' => '1721G',
));
dict_add('ClassementType', array(
  'fr' => 'Type de classement',
  'nl' => 'Rangschikkingsysteem',
  'en' => 'Ranking Type',
  'mk' => 'Тип на рангирање',
));
dict_add('ClassementTypeOfficial', array(
  'fr' => 'Officiel (ancien)',
  'nl' => 'Officieel (oud)',
  'en' => 'Official (old)',
  'mk' => 'Официјален (стар)',
));
dict_add('ClassementTypeOfficialNew', array(
  'fr' => 'Officiel',
  'nl' => 'Officieel',
  'en' => 'Official',
  'mk' => 'Официјален',
));
dict_add('ClassementTypePingouin', array(
  'fr' => 'Pingouin',
  'nl' => 'Pingouin',
  'en' => 'Pingouin',
  'mk' => 'Пингвин',
));
dict_add('ClassementType4Points', array(
  'fr' => '4 points',
  'nl' => '4 punten',
  'en' => '4 points',
  'mk' => '4 points',
));
dict_add('ClassementTypeWonMatches', array(
  'fr' => 'Vict. Indiv.',
  'nl' => 'Indiv. Overw.',
  'en' => 'Indiv. Vict.',
  'mk' => 'Indiv. Vict.',
));
dict_add('ClassementTypeSporcrea', array(
  'fr' => 'Sporta',
  'nl' => 'Sporta',
  'en' => 'Sporta',
  'mk' => 'Sporta',
));
dict_add('ClassementTypeSporcrea3pts', array(
  'fr' => 'Sporta (3 points)',
  'nl' => 'Sporta (3 punten)',
  'en' => 'Sporta (3 points)',
  'mk' => 'Sporta (3 points)',
));
dict_add('ClassementTypeKavvvBeker', array(
  'fr' => 'KAVVV Coupe',
  'nl' => 'KAVVV Beker',
  'en' => 'KAVVV Cup',
  'mk' => 'KAVVV Cup',
));
dict_add('ClassementHelp', array(
  'fr' => 'Aide sur les types de classement',
  'nl' => 'Hulp over rangschikkingsystemen',
  'en' => 'Help on ranking systems',
  'mk' => 'Помош со системот за рангирање',
));
dict_add('PerVictory', array(
  'fr' => 'Par victoire',
  'nl' => 'Per overwinning',
  'en' => 'Per victory',
  'mk' => 'По добиени',
));
dict_add('PerPoints', array(
  'fr' => 'Par points',
  'nl' => 'Per punten',
  'en' => 'Per points',
  'mk' => 'По поени',
));
dict_add('ClassementPerVictory', array(
  'fr' => 'Classement individuel par victoire',
  'nl' => 'Individuele Rangschikking per overwinning',
  'en' => 'Individual Ranking per Victory',
  'mk' => 'Индивидуално рангирање по добиени',
));
dict_add('PersonalList', array(
  'fr' => 'Liste personnelle',
  'nl' => 'Persoonlijke lijst',
  'en' => 'Personal list',
  'mk' => 'Индивидуална листа',
));
dict_add('WOAsVictory', array(
  'fr' => 'Les W-O sont considérés comme des victoires<br>dans les classements individuels',
  'nl' => 'W-O tellen zoals een overwinning<br>in de individuele rangschikkingen',
  'en' => 'WO are considered as a victory<br>in individual rankings',
  'mk' => 'Кај индивидуално рангирање<br>WO се смета за добиена',
));
dict_add('Download', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('NewClassementDowload', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('ELOPoints', array(
  'fr' => 'Points ELO',
  'nl' => 'ELO punten',
  'en' => 'ELO points',
  'mk' => 'ELO поени',
));
dict_add('PlayerIndexNotValid', array(
  'fr' => 'L\'index [#PLAYERID#] n\'est pas valide (doit être un chiffre)',
  'nl' => 'De spelersindex [#PLAYERID#] is niet geldig (moet een nummer zijn)',
  'en' => 'Player index [#PLAYERID#] is not valid (must be a number)',
  'mk' => 'Шифрата на играчот [#PLAYERID#] е невалиден (мора да биде бројка)',
));
dict_add('PlayerFirstNameNotValid', array(
  'fr' => 'Le prénom du joueur n\'est pas valide (doit être non vide)',
  'nl' => 'De voornaam van de speler niet geldig is (mag niet leeg zijn)',
  'en' => 'Player first name is not valid (cannot be empty)',
  'mk' => 'Името на играчот е невалидно (неможе да биде празно)',
));
dict_add('PlayerLastNameNotValid', array(
  'fr' => 'Le nom du joueur n\'est pas valide (doit être non vide)',
  'nl' => 'De naam van de speler niet geldig is (mag niet leeg zijn)',
  'en' => 'Player last name is not valid (cannot be empty)',
  'mk' => 'Презимето на играчот е невалидно (неможе да биде празно)',
));
dict_add('FRBTT', array(
  'fr' => 'FRBTT',
  'nl' => 'KBTTB',
  'en' => 'RBTTF',
  'mk' => 'RBTTF',
));
dict_add('NoClubCategoryDefined', array(
  'fr' => 'Pas de catégorie de clubs par défaut, vérifiez votre configuration.',
  'nl' => 'Geen default clubcategorie, gelieve de configuratie te verifieren.',
  'en' => 'No default club cateory defined, please check your configuration.',
  'mk' => 'Нема преддефинирана категорија за клубот, ве молам проверете ја вашата конфигурација.',
));
dict_add('NoDefaultSeason', array(
  'fr' => 'Pas de saison par défaut, vérifiez votre configuration.',
  'nl' => 'Geen default seizoen, gelieve de configuratie te verifieren.',
  'en' => 'No default season defined, please check your configuration.',
  'mk' => 'Нема преддефинирана сезона, ве молам проверете ја вашата конфигурација.',
));
dict_add('EnterPlayerID', array(
  'fr' => 'Veuillez entrer le numéro d\'identification du joueur:',
  'nl' => 'Gelieve een identificatienummer in te vullen:',
  'en' => 'Please enter player ID:',
  'mk' => 'Внесете шифра на играч',
));
dict_add('EnterPlayerIdOrUserId', array(
  'fr' => 'Veuillez entrer le numéro d\'identification de l\'utilisateur (ou son login):',
  'nl' => 'Gelieve een identificatienummer of de gebruikersnaam in te vullen:',
  'en' => 'Please enter user ID (or login):',
  'mk' => 'Внесете шифра на корисник (или корисничко име)',
));
dict_add('InvalidPlayerID', array(
  'fr' => 'Le numéro d\'identification donné n\'est pas valable.',
  'nl' => 'Het gegeven identificatienummer is niet geldig.',
  'en' => 'The given player ID is not valid.',
  'mk' => 'Внесената шифра на играч не е валидна.',
));
dict_add('CommentBy', array(
  'fr' => 'Par',
  'nl' => 'Door',
  'en' => 'By',
  'mk' => 'Од',
));
dict_add('CommentOn', array(
  'fr' => 'le',
  'nl' => 'op',
  'en' => 'on',
  'mk' => 'на',
));
dict_add('ResultsTimestamp', array(
  'fr' => 'Derni&egrave;re remise à jour des résultats',
  'nl' => 'Laatste update van de resultaten',
  'en' => 'Results last update',
  'mk' => 'Последно ажурирање на резултати',
));
dict_add('LastUpdateTimestamp', array(
  'fr' => 'Derni&egrave;re remise à jour du classement',
  'nl' => 'Laatste update van de rangschikking',
  'en' => 'Ranking last update',
  'mk' => 'Последно ажурирање на рангирање',
));
dict_add('CalendarDownload', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('FinesDownload', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('ClassementDownload', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('PlayerListDownload', array(
  'fr' => 'Télécharger',
  'nl' => 'Download',
  'en' => 'Download',
  'mk' => 'Преземи',
));
dict_add('MaxTeamValue', array(
  'fr' => 'Max. valeur',
  'nl' => 'Max. waarde',
  'en' => 'Max. value',
  'mk' => 'Макс. вредност',
));
dict_add('MaxTeamValueAdmin', array(
  'fr' => 'Admin Max. valeur',
  'nl' => 'Max. waarde Admin',
  'en' => 'Admin Max. value',
  'mk' => 'Админ макс. вредност',
));
dict_add('Explanation', array(
  'fr' => 'Instructions',
  'nl' => 'Inleiding',
  'en' => 'Help',
  'mk' => 'Помош',
));
dict_add('ControlGroup', array(
  'fr' => 'Groupe de contrôle',
  'nl' => 'Controlegroep',
  'en' => 'Control group',
  'mk' => 'Контролна група',
));
dict_add('Beker', array(
  'fr' => 'Coupe',
  'nl' => 'Beker',
  'en' => 'Cup',
  'mk' => 'Чаша',
));
dict_add('DivisionURLPath', array(
  'fr' => 'division',
  'nl' => 'afdeling',
  'en' => 'division',
  'mk' => 'дивизија',
));
dict_add('CalendarICSURLPath', array(
  'fr' => 'division/ics',
  'nl' => 'afdeling/ics',
  'en' => 'division/ics',
  'mk' => 'дивизија/ics',
));
dict_add('CalendarDownloadICS', array(
  'fr' => 'Importer le calendrier',
  'nl' => 'Kalender importeren',
  'en' => 'Import calendar',
  'mk' => 'Импортирај календар',
));
dict_add('CalendarICSExplanation', array(
  'fr' => 'Utilisez cette adresse pour accéder à l\'agenda depuis d\'autres applications. Copiez l\'adresse et collez-la dans n\'importe quelle application de type agenda prenant en charge le format iCal.',
  'nl' => 'Gebruik dit adres om je agenda te openen vanuit andere toepassingen. Je kunt het adres kopiëren en in elke agendatoepassing plakken waarin de iCal-indeling wordt ondersteund.',
  'en' => 'Please use thi address to access your calendar from other applications. You can copy and paste this into any calendar product that supports the ical format.',
  'mk' => 'Please use thi address to access your calendar from other applications. You can copy and paste this into any calendar product that supports the ical format.',
));
dict_add('WarningMessageForSynchronizedDivision', array(
  'fr' => 'Cette division n\'est pas gérée par ce système mais est automatiquement synchronisée à partir d\'une source de données externe.',
  'nl' => 'Deze afdeling wordt niet door dit systeem beheerd maar is automatisch uit een andere databank gesynchronisseerd.',
  'en' => 'This division is not managed by this system.  Data comes from an external database.',
  'mk' => 'This division is not managed by this system.  Data comes from an external database.',
));
dict_add('WarningMessageForSynchronizedDivision-AF', array(
  'fr' => 'Cette division n\'est pas gérée par ce système mais est automatiquement synchronisée à partir d\'une source de données externe.',
  'nl' => 'Deze afdeling wordt niet door dit systeem beheerd maar is automatisch uit een andere databank gesynchronisseerd.',
  'en' => 'This division is not managed by this system.  Data comes from an external database.',
  'mk' => 'This division is not managed by this system.  Data comes from an external database.',
));
dict_add('WarningMessageForSynchronizedDivision-NAT', array(
  'fr' => 'Cette division est partiellement gérée par ce système.  Certains résultas proviennent d\'une source de données externe..',
  'nl' => 'Deze afdeling wordt maar partieel door dit systeem beheerd.  Sommige wedstrijden worden automatisch uit een andere databank gesynchronisseerd.',
  'en' => 'This division is only partially managed by this system.  Some results come from an external system.',
  'mk' => 'This division is only partially managed by this system.  Some results come from an external system.',
));
dict_add('WarningMessageForSynchronizedDivisionSource', array(
  'fr' => 'Voir la source',
  'nl' => 'Zie de bron',
  'en' => 'See data source',
  'mk' => 'Прикажи извор на податоци',
));
dict_add('BELRankingCategory', array(
  'fr' => 'Catégorie pour le système de classement <i>BEL</i>',
  'nl' => 'Categorie voor het <i>BEL</i> rangschikkingsysteem',
  'en' => 'Category for the <i>BEL</i> ranking system',
  'mk' => 'Категорија за <i>BEL</i> систем на рангирање',
));
dict_add('NoBELCategory', array(
  'fr' => 'Aucune',
  'nl' => 'Niet gespecifieerd',
  'en' => 'No category',
  'mk' => 'Нема категорија',
));
dict_add('SeniorHommesShort', array(
  'nl' => 'HER',
  'en' => 'HER',
  'mk' => 'МАЖ',
));
dict_add('SeniorDamesShort', array(
  'nl' => 'DAM',
  'en' => 'DAM',
  'mk' => 'ЖЕН',
));
dict_add('SeniorDamesShort', array(
  'nl' => 'DAM',
  'en' => 'DAM',
  'mk' => 'ЖЕН',
));
dict_add('DrawTypeRandom', array(
  'fr' => 'Aléatoire',
  'nl' => 'Elke ronde trekking',
  'en' => 'Random',
  'mk' => 'Случаен избор',
));
dict_add('DrawTypeStraight', array(
  'fr' => 'Selon le tableau d\'inscription',
  'nl' => 'Eindtabel',
  'en' => 'According registration order',
  'mk' => 'Според редослед на регистрација',
));
dict_add('AnonymousPlayerName', array(
  'fr' => 'JOUEUR',
  'nl' => 'SPELER',
  'en' => 'PLAYER',
  'mk' => 'ИГРАЧ',
));
dict_add('AllWeeks', array(
  'fr' => "Toutes les semaines",
  'nl' => 'Alle weken',
  'en' => 'All weeks',
  'mk' => 'Сиде недели',
));
dict_add('AllWeeksShort', array(
  'fr' => "Toutes",
  'nl' => 'Alle',
  'en' => 'All',
  'mk' => 'Сите',
));
dict_add('Menu', array(
  'fr' => "Menu",
  'nl' => 'Menu',
  'en' => 'Menu',
  'mk' => 'Мени',
));
dict_add('Redirecting', array(
  'fr' => "Veuillez patientez quelques instants...",
  'nl' => 'Gelieve even te wachten...',
  'en' => 'Please wait...',
  'mk' => 'Ве молам причекајте...',
));
dict_add('GameDifference', array(
  'fr' => "Sets",
  'nl' => 'Sets',
  'en' => 'Sets',
  'mk' => 'Сетови',
));
dict_add('InvalidFirstMatchNumber', array(
  'fr' => "Le numéro du premier match est invalide. Celui-ci doit être un nombre.",
  'nl' => 'Het nummer van de eerste wedstrijd is niet geldig.  Deze moet numeriek zijn.',
  'en' => 'The number of the first match is not valid.  It must be a number.'
));
dict_add('InvalidPromotedTeamsCount', array(
  'fr' => "Le nombre d'équipes promues doit être une valeur numérique.",
  'nl' => 'Het aantal stijgers moet een nummer zijn.',
  'en' => 'The number of promoted teams must be numeric.'
));
dict_add('InvalidRelegatedTeamsCount', array(
  'fr' => "Le nombre d'équipes reléguées doit être une valeur numérique.",
  'nl' => 'Het aantal dalers moet een nummer zijn.',
  'en' => 'The number of relegated teams must be numeric.'
));
dict_add('PerWorldRanking', array(
  'fr' => "Classement mondial",
  'nl' => 'Wereldranglijst',
  'en' => 'World Ranking'
));
dict_add('ResetClubFilter', array(
  'fr' => "Annuler le filtre (sélectionner tous les clubs)",
  'nl' => 'De filter annuleren (alle clubs kiezen)',
  'en' => 'Reset filter (select all clubs)'
));

if ($use_cache && function_exists('apc_store')) {
  apc_store('dictionary', $dictionary);
}
?>
