<?php
/** \internal
 ****************************************************************************
 * TabT API
 *  A programming interface to access information managed
 *  by TabT, the table tennis information manager.
 * -----------------------------------------------------------------
 * TabT API type descriptions
 * -----------------------------------------------------------------
 * @version 0.7.6
 * -----------------------------------------------------------------
 * Copyright (C) 2007-2011 Gaëtan Frenoy (gaetan@frenoy.net)
 * -----------------------------------------------------------------
 * This file is part of TabT API
 *
 * TabT API is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TabT API.  If not, see <http://www.gnu.org/licenses/>.
 **************************************************************************/

/**
 * @defgroup TabTAPItypes TabT API type descriptions
 *
 * @brief Here are all types used by TabT API functions
 *
 * @author Gaëtan Frenoy <gaetan@frenoy.net>
 * @version 0.7.6
 */

////////////////////////////////////////////////////////////////////////////

/**
 * @struct CredentialsType
 *
 * @brief Defines credentials to connect to a TabT website
 *
 * Some functions may require special rights to be processed successfully.
 * The account (username and password) to be used is the same than the one
 * used to connect to the TabT website.
 *
 * @see Test, GetSeasons, GetClubTeams, GetDivisionRanking, GetMatches, GetMembers, Upload, GetClubs
 * @ingroup TabTAPItypes
 */
class CredentialsType {
  /**
   * The user name on the TabT website you want to gather data from
   *
   * @b type: string
   */
  public $Account;
  /**
   * Your password
   *
   * @b type: string
   */
  public $Password;
}

/**
 * @struct TestRequest
 *
 * @brief Input parameters of the ::Test API
 *
 * @see Test
 * @ingroup TabTAPItypes
 */
class TestRequest {
  /**
   * Defines credentials (username and password) to connect to a TabT website
   *
   * @b type: ::CredentialType
   */
  public $Credentials;
}

/**
 * @struct TestResponse
 *
 * @brief Output parameters of the ::Test API
 *
 * @see Test
 * @ingroup TabTAPItypes
 */
class TestResponse {
  /**
   * Server time when responding to this test query
   */ 
  public $Timestamp;
  /**
   * Current API verion running on the server
   */
  public $ApiVersion;
  /**
   * Checks if given credentials are valid
   *
   * Returns true if account and password are correctly
   * verified on the server
   */
  public $IsValidAccount;
  /**
   * Language used when replying to requests
   *
   * Default language is English but if valid credentials
   * are given, user's language will be used.
   * To change this language, one has to connect to the
   * TabT web interface and select his/her preferred language
   */
  public $Language;
  /**
   * Name of the database currently in use
   * (eg Spocrea, VTTL, ...)
   */
  public $Database;
}

/**
 * @struct GetSeasonsResponse
 *
 * @brief Output parameters of the ::GetSeasons API
 *
 * @see GetSeasons
 * @ingroup TabTAPItypes
 */
class GetSeasonsResponse
{
  /**
   * @brief The season identifier of the current season
   *
   * This is a positive integer.
   *
   * @b type:  int
   */
  public $CurrentSeason;

  /**
   * The name of current season.
   *
   * @b type:  string
   */
  public $CurrentSeasonName;

  /**
   * The list of seasons, each season is given as a ::SeasonEntry
   *
   * Example if 2008-2009 is the current season
   * <ul>
   *  <li><code>1 | 2001-2002 | false</code></li>
   *  <li><code>2 | 2002-2003 | false</code></li>
   *  <li>(...)</li>
   *  <li><code>7 | 2007-2008 | false</code></li>
   *  <li><code>8 | 2008-2009 | true</code></li>
   * </ul>
   *
   * @see SeasonEntry
   * @b type:  SeasonEntry[]
   */
  public $SeasonEntries;
}

/**
 * @struct SeasonEntry
 *
 * @brief Information about one season
 *
 * @see GetSeasons, GetSeasonsResponse
 * @ingroup TabTAPItypes
 */
class SeasonEntry
{
  /**
   * Identifier of the season
   *
   * @b type:  int
   */
  public $Season;
  
  /**
   * The name of the season.
   *
   * @b type:  string
   */
  public $Name;
  
  /**
   * Indicates if the season is the current season
   *
   * true if the season is the current season, false otherwise
   *
   * @b type:  boolean
   */
  public $IsCurrent;
}

/**
 * @struct GetClubTeamsRequest
 *
 * @brief Input parameters of the ::GetClubTeams API
 *
 * @see GetClubTeams
 * @ingroup TabTAPItypes
 */
class GetClubTeamsRequest
{
  /**
   * Defines credentials to connect to a TabT website
   *
   * @b type:  CredentialsType
   */
  public $Credentials;

  /**
   * The club wherein the teams are playing.
   *
   * This parameter contains the unique identifier of the club where the team(s)
   * returned by ::GetClubTeams belongs to.
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * @b type:  string
   */
  public $Club;

  /**
   * The season when the teams have played. 
   *
   * This parameter is optional, default is the current season (see ::GetSeasons)
   *
   * @b type:  int
   */
  public $Season;
}

/**
 * @struct GetClubTeamsResponse
 *
 * @brief Output parameters of the ::GetClubTeams API
 *
 * @see GetClubTeams
 * @ingroup TabTAPItypes
 */
class GetClubTeamsResponse
{
  /**
   * Name of the requested club (see Club in ::GetClubTeamsRequest).
   *
   * If available, the short name of the club is given
   *
   * @b type:  string
   */
  public $ClubName;

  /**
   * The number of teams returned by ::GetClubTeams
   *
   * This is also the number of entries that will be returned in TeamEntries
   *
   * @b type:  int
   */
  public $TeamCount;


  /**
   * The list of teams matching criteria given in ::GetClubTeamsRequest
   *
   * Each element of the list is a ::TeamEntry structure.
   *
   * @b type:  TeamEntry
   */
  public $TeamEntries;
}

/**
 * @struct TeamEntry
 *
 * @brief A team of a club
 *
 * ::GetClubTeams returns a list of teams for a given club.  This structure contains
 * the information about each team.
 *
 * @see GetClubTeams, GetClubTeamsResponse
 * @ingroup TabTAPItypes
 */
class TeamEntry
{
  /**
   * The unique identified of the team.
   *
   * The system will generate a unique identifier for each team of the club.
   *
   * @b type:  string
   */
  public $TeamId;
  
  /**
   * The team letter.
   *
   * Usually each time of a club is identified by a letter. Eg team "A", "B", "C" etc.
   * In some cases, no letter is given and this parameter is empty.
   *
   * @b type:  string
   */
  public $Team;
  
  /**
   * The unique identified of the division wherein the team is playing.
   *
   * @b type:  int
   */
  public $DivisionId;
  
  /**
   * The name of the division wherein the team is playing
   *
   * Example for division #390 of VTTL competition (season 2007-2008):
   * <ul>
   *  <li>with an account configured for Dutch: <code>Afdeling 2A - Prov. Vl.-B/Br. - Heren</code></li>
   *  <li>with an account configured for French: <code>Division 2A - Prov. Vl.-B/Br. - Hommes</code></li>
   * </ul>
   *
   * The name is localized according to the ::CredentialsType credentials given in the
   * ::GetClubTeamsRequest request.  If not specified, the default language is defined by
   * the TabT website administrator.
   *
   * @b type:  string
   */
  public $DivisionName;
  
  /**
   * The category of the division wherein the team is playing
   *
   * Divisions are grouped by category.  All divisions belonging to the same category are sharing
   * the same properties like the type of match (3 vs 3 or 4 vs 4) and the player ranking system
   * (men or women).
   *
   * @b type:  int
   */
  public $DivisionCategory;
}


/**
 * @struct GetDivisionRankingRequest
 *
 * @brief Input parameters of the ::GetDivisionRanking API
 *
 * @see GetDivisionRanking
 * @ingroup TabTAPItypes
 */
class GetDivisionRankingRequest
{
  /**
   * Defines credentials to connect to a TabT website
   *
   * @b type:  CredentialsType
   */
  public $Credentials;

  /**
   * The unique identifier of the requested division
   * 
   * @b type:  int
   */
  public $DivisionId;

  /**
   * The week name the ranking should be given for
   *
   * This parameter is optional.
   *
   * @b type:  string
   */
  public $WeekName;

  /**
   * The type of ranking system to use.
   *
   * The main systems are:
   *  1 : Classic  (as described in "Landelijke Sportreglement at § C.7)
   *  2 : Overloop 
   *  3 : 4-a-win
   *  4 : Per individual victory
   *  5 : Sporcrea
   *  6 : Classic 2009 (new rules as of season 2009-2010)
   * See also <code>http://tabt.frenoy.net/index.php?display=TabTWebDocTeamClassementTypes</code>
   *
   * This parameter is optional.  If not specified, the ranking system configured for the
   * requested division will be used.
   *
   * @b type:  string
   */
  public $RankingSystem;
}

/**
 * @struct GetDivisionRankingResponse
 *
 * @brief Output parameters of the ::GetDivisionRanking API
 *
 * @see GetDivisionRanking
 * @ingroup TabTAPItypes
 */
class GetDivisionRankingResponse
{
  /**
   * Full localized name of the requested division
   *
   * Example for division #390 of VTTL competition (season 2007-2008):
   * <ul>
   *  <li>with an account configured for Dutch: <code>Afdeling 2A - Prov. Vl.-B/Br. - Heren</code></li>
   *  <li>with an account configured for French: <code>Division 2A - Prov. Vl.-B/Br. - Hommes</code></li>
   * </ul>
   *
   * The name is localized according to the ::CredentialsType credentials given in the
   * ::GetClubTeamsRequest request.  If not specified, the default language is defined by
   * the TabT website administrator.
   *
   * @b type:  string
   */
  public $DivisionName;

  /**
   * List of lines of the ranking, each line contain the information about one team of the division
   *
   * Example for division #390 after 18 weeks of VTTL competition (season 2007-2008):
   * <ul>
   *  <li><code>1 | T.T. Groot-Bijgaarden A | 18 | 13 | 2 | 3 | 196 |  92 | 641 | 378 | 29</code></li>
   *  <li><code>2 | Werchter B              | 18 | 11 | 4 | 3 | 158 | 130 | 525 | 400 | 25</code></li>
   *  <li><code>3 | Hurricane TTW C         | 17 | 10 | 2 | 5 | 167 | 104 | 610 | 440 | 25</code></li>
   *  <li><code>4 | T.T.K. Vilvo F          | 17 | 10 | 6 | 1 | 165 | 107 | 525 | 441 | 21</code></li>
   *  <li>(...)</li>
   * </ul>
   *
   * @b type:  RankingEntry[]
   * @see RankingEntry
   */
  public $RankingEntries;
}

/**
 * @struct RankingEntry
 *
 * @brief Information about a team listed in a ::GetDivisionRanking ranking
 *
 * @see GetDivisionRanking, GetDivisionRankingResponse
 * @ingroup TabTAPItypes
 */
class RankingEntry
{
  /**
   * The position of the team within the ranking
   *
   * This is positive integer starting at 1.  Ex-aequo may have the same position in the ranking
   *
   * @b type:  int
   */
  public $Position;

  /**
   * The name of the team
   *
   * The name includes the team letter.  If available, the short name of the club is given.
   * Example: <code>Hurricane C</code> or <code>Werchter A</code>
   *
   * @b type:  string
   */
  public $Team;

  /**
   * The number of games played by the team
   *
   * If some games have been delayed, all the teams do not have played the same amount of matches
   *
   * @b type:  int
   */
  public $GamesPlayed;

  /**
   * The number of games won by the team
   *
   * @b type:  int
   */
  public $GamesWon;

  /**
   * The number of games lost by the team
   *
   * @b type:  int
   */
  public $GamesLost;

  /**
   * The number of draw game made by the team
   *
   * @b type:  int
   */
  public $GamesDraw;

  /**
   * The number of individual matches won by all players of the team
   *
   * @b type:  int
   */
  public $IndividualMatchesWon;

  /**
   * The number of individual matches lost by all players of the team
   *
   * @b type:  int
   */
  public $IndividualMatchesLost;

  /**
   * The number of sets won by all players of the team during their individual matches
   *
   * @b type:  int
   */
  public $IndividualSetsWon;

  /**
   * The number of sets won by all players of the team during their individual matches
   *
   * @b type:  int
   */
  public $IndividualSetsLost;

  /**
   * The number of points or the "score" won by the team
   *
   * This information is used to rank the teams.  This is usually a number of points (2 for a win, 1 for a draw)
   * but some ranking system may be different.
   *
   * @b type:  int
   */
  public $Points;
}

/**
 * @struct GetMembersRequest
 *
 * @brief Input parameters of the ::GetMembers API
 *
 * @see GetMembers
 * @ingroup TabTAPItypes
 */
class GetMembersRequest
{
  /**
   * Defines credentials to connect to a TabT website
   * @b type:  CredentialsType
   */
  public $Credentials;

  /**
   * The club the players are members of
   *
   * This parameter contains the unique identifier of the club where the players(s)
   * returned by ::GetMembers belongs to.
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * This parameter is optional.  But at least one search criteria has to be specified (club, unique index or name).
   * If this parameter is specified, the response will not contain the player club.
   *
   * @b type:  string
   */
  public $Club;

  /**
   * The season when the players were members of the requested club
   *
   * This parameter is optional, default is the current season (see ::GetSeasons)
   *
   * @b type:  int
   */
  public $Season;

  /**
   * The identifier of the player category to be considered
   *
   * Usually 1 for men and 2 for women.
   * This parameter is optional, default is the first player category which is usually the senior men.
   *
   * @b type:  int
   */
  public $PlayerCategory;

  /**
   * The unique index of the requested member.
   *
   * This parameter is optional.  But at least one search criteria has to be specified (club, unique index or name).
   *
   * @b type:  int
   */
  public $UniqueIndex;

  /**
   * The pattern the name of the request member must match.  The wildcard character is the percent sign (%).
   * Both last name and first name are searched.  The search is not case sensitive.
   *
   * This parameter is optional.  But at least one search criteria has to be specified (club, unique index or name).
   *
   @ var string
   */
  public $NameSearch;

  /**
   * Returns extended information about the returned members : status, gender, category and birthdate
   *
   * If set to "true", valid credential has to be specified.
   * Birthdate will only be given if given credential have administrive rights.
   *
   * This parameter is optional.  Default value is false.
   *
   * @b type:  boolean
   */
  public $ExtendedInformation;
}

/**
 * @struct GetMembersResponse
 *
 * @brief Output parameters of the ::GetMembers API
 *
 * @see GetMembers
 * @ingroup TabTAPItypes
 */
class GetMembersResponse
{
  /**
   * The position (order) of the member on the list
   *
   * This number is unique and start at 1.
   *
   * @b type:  int
   */
  public $Position;

  /**
   * The unique index of the member
   *
   * Each player is given a unique number, usually given by the federation he/she is belonging to.
   *
   * Examples: <code>VTTL id of GAËTAN FRENOY is 505290</code>, <code>Sporcrea id of JOHAN DE KONINCK is 8196</code>.
   *
   * @b type:  int
   */
  public $UniqueIndex;

  /**
   * A special index to group player with the same ranking
   *
   * All players that have the same ranking will receive the same ranking index.  This index is the position of
   * the last player of this ranking.  This index can optionally be used in TabT.
   *
   * @b type:  int
   */
  public $RankingIndex;

  /**
   * The given name of the player
   *
   * @b type:  string
   */
  public $FirstName;

  /**
   * The family name of the player
   *
   * @b type:  string
   */
  public $LastName;

   /**
    * The ranking of the player
    *
    * @b type:  string
    */
  public $Ranking;

   /**
    * The status of the player
    *
    * Common status are: A = Active, R = Recreative, V = Reserve
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  string
    */
  public $Status;

   /**
    * The club index of the player
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  string
    */
  public $Club;

   /**
    * The player's gender
    *
    * M = male, F = female
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  string
    */
  public $Gender;

   /**
    * The player's age category
    *
    * Common catogory are: SEN, VET, MIN, ...
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  date
    */
  public $Category;

   /**
    * The player's birthdate
    *
    * Format is YYYY-MM-DD
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  date
    */
  public $BirthDate;

   /**
    * Is true if the player sent a valid medical attestation
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  boolean
    */
  public $MedicalAttestation;

}

/**
 * @struct GetMatchesRequest
 *
 * @brief Input parameters of the ::GetMatches API
 *
 * @see GetMatches
 * @ingroup TabTAPItypes
 */
class GetMatchesRequest
{
  /**
   * Defines credentials to connect to a TabT website
   *
   * This parameter is optional.
   *
   * @b type:  CredentialsType
   */
  public $Credentials;

  /**
   * The internal identifier of the requested division
   *
   * This parameter is optional.  If this parameter is not given, Club must be given (and vice versa)
   *
   * @b type:  int
   */
  public $DivisionId;

  /**
   * The club where the return team(s) play
   *
   * This parameter is optional.  If this parameter is not given, DivisionId must be given (and vice versa)
   *
   * If set, this parameter contains the unique identifier of the club where the team(s)
   * returned by ::GetMatches belongs to.
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * @b type:  string
   */
  public $Club;

  /**
   * The team letter to be returned (within a given club)
   *
   * This parameter is optional.  If this parameter is set then Club parameter must also have been given.
   *
   * @b type:  string
   */
  public $Team;

  /**
   * The division category the matches were played in.
   *
   * This parameter is optional.  If not given all matches of all categories are returned.
   *
   * @b type:  int
   */
   public $DivisionCategory;

   /**
   * The season when the matches have been played. 
   *
   * This parameter is optional, default is the current season (see ::GetSeasons).
   * If given in conjuction with DivisionId, the returned list of matches may be empty because
   * the division does not belong to the given season.
   *
   * @b type:  int
   */
  public $Season;

  /**
   * The name of week when the matches have been played.
   *
   * This parameter is optional.  If not given all matches of all weeks are returned.
   *
   * @b type:  string
   */
  public $WeekName;

  /**
   * The level the matches were played in.
   *
   * This parameter is optional.  If not given all matches of all levels are returned.
   * A level is a logical group of divisions (eg per provinces or regions).
   *
   * @b type:  int
   */
   public $Level;

  /**
   * Indicates if the response should include the name of the division.
   *
   * This parameter is optional.  If not given, the name of the division is not returned.
   * Possible values are:
   * <ul>
   *  <li>no   : the division name is not given (this is the default)</li>
   *  <li>yes  : the full division is given (eg <code>Afdeling 2A - Prov. Vl.-B/Br. - Heren</code>)</li>
   *  <li>short: a short name of the division is given (eg <code>2A</code>)</li>
   * </ul>
   *
   * @b type:  ShowDivisionNameType
   */
   public $ShowDivisionName;
}

/**
 * @struct GetMatchesResponse
 *
 * @brief Ouput parameters of the ::GetMatches API
 *
 * @see GetMatches
 * @ingroup TabTAPItypes
 */
class GetMatchesResponse
{
  /**
   * The number of matches returned by ::GetMatches
   *
   * This is also the number of entries that will be returned in ::TeamMatchEntries
   *
   * @b type:  int
   */
  public $MatchCount;

  /**
   * List of all games matching the requested criteria (see ::GetMatchesRequest).
   *
   * Each element of the list is a ::TeamMatchEntry
   *
   * @b type:  TeamMatchEntry[]
   * @see TeamMatchEntry
   */
  public $TeamMatchEntries;
}

/**
 * @struct TeamMatchEntry
 *
 * @brief Information about a match between two teams
 *
 * This structure contains data related to a fixture between two competitors, including the name of the teams, the
 * date and time of match and, if already played, the ending score.
 *
 * @see GetMatches, GetMatchesResponse
 * @ingroup TabTAPItypes
 */
class TeamMatchEntry
{
  /**
   * The name of the division wherein the match has been played.
   *
   * This name is only given if explicitely requested in ::GetMatchesRequest.
   * 
   * @b type:  string
   */
  public $DivisionName;

  /**
   * A identifier for the match
   *
   * This code is given by the championship organisator.  It is usually the combination of the week and a unique number within
   * the category.  Example: <code>01/002</code> or <code>12/121</code>.  But this number is not unique among all the matches.
   * 
   * @b type:  string
   */
  public $MatchId;

  /**
   * The name of the week (round) when the match is scheduled
   *
   * This is usually a number but can sometimes be a letter.
   * Example: <code>01</code>, <code>14</code>, <code>D</code>
   *
   * @b type:  string
   */
  public $WeekName;

  /**
   * The date when the match is scheduled
   *
   * Format is YYYY-MM-DD where
   * <ul>
   *   <li>YYYY is the year on 4 digits (eg 2008)</li>
   *   <li>MM is the month on 2 digits (eg 06)</li>
   *   <li>DD is the day of the month on 2 digits (eg 29)</li>
   * </ul>
   *
   * @b type:  Date
   */
  public $Date;

  /**
   * The time when the match is scheduled
   *
   * Format is HH:MM where
   * <ul>
   *   <li>HH is the number of hours on two digits (eg 19)</li>
   *   <li>MM is the number of minutes on two digits (eg 45)</li>
   * </ul>
   * Some examples: <code>14:00</code>, <code>19:00</code>, <code>19:45</code>.
   *
   * @b type:  Time
   */
  public $Time;

  /**
   * The index of the venue where the match has to be played
   *
   * The unique identifier of the venue where the match has to be played.  For clubs
   * that do have only one venue, the value is always 1.
   * To get the list of venues of a club, see ::GetClubs
   *
   * @b type:  integer
   */
  public $Venue;

  /**
   * The club index of the visited team
   *
   * The unique identifier of the club of the visited team
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * @b type:  string
   */
  public $HomeClub;

  /**
   * The name of the visited team
   *
   * The name includes the team letter.  If available, the short name of the club is given.
   * Example: <code>Hurricane C</code> or <code>Werchter A</code>
   *
   * @b type:  string
   */
  public $HomeTeam;

  /**
   * The club index of the visiting team
   *
   * The unique identifier of the club of the visiting team
   * Example : <code>2054</code> for Sporcrea club of TTG Mollem.
   *
   * @b type:  string
   */
  public $AwayClub;

  /**
   * The name of the visiting team
   *
   * The name includes the team letter.  If available, the short name of the club is given.
   * Example: <code>Hurricane C</code> or <code>Werchter A</code>
   *
   * @b type:  string
   */
  public $AwayTeam;

  /**
   * If the game has already been played, the result of the game.
   *
   * The format is HH-AA where
   * <ul>
   *   <li>HH is the score of the home team (eg 2)</li>
   *   <li>AA is the score of the away team (eg 14)</li>
   * </ul>
   * Some examples: <code>14-2</code>, <code>9-1</code>, <code>4-3</code>
   *
   * @b type:  string
   */
  public $Score;

  /**
   * A unique (internal) identifier for the match
   *
   * This code is given by the TabT application.  It is a positive integer that is unique among
   * all matches.  It can be use to reference a match on the TabT website.
   * Example: match #<code>21780</code> is match P01/165 WVL128 Waregem D vs WVL110 Gullegem K
   * in West-Vlaanderen for season 2007-2008.  It can be directly access using the link
   * <code>http://competitie.vttl.be/match/21780</code>
   *
   * @b type:  string
   */
  public $MatchUniqueId;
}

/**
 * @struct UploadRequest
 *
 * @brief Input parameters of the ::Upload API
 *
 * @see Upload
 * @since Version 0.6
 * @ingroup TabTAPItypes
 */
class UploadRequest {
  /**
   * Defines credentials (username and password) to connect to a TabT website
   *
   * Unlike the other APIs, this parameter is required for uploading data.
   */
  public $Credentials;

  /**
   * Upload data
   *
   * Data is a list of lines.  Each line is ended by a \\n or a \\r\\n character.
   * Syntax of each line is explained on http://tabt.frenoy.net/
   * (see TabT-Upload format)
   */
  public $Data;
}

/**
 * @struct UploadResponse
 *
 * @brief Output parameters of the ::Upload API
 *
 * @see Upload
 * @ingroup TabTAPItypes
 */
class UploadResponse {
  /**
   * The global result of the upload.
   */ 
  public $Result;

  /**
   * The number of lines processed
   */ 
  public $ProcessedLineCount;

  /**
   * For each erroneous lines, gives a description of the error.
   *
   * If "Result" of ::UploadResponse is false, at least one line of the uploaded data has been rejected.
   * For each rejected and erroneous line, a description of the error will be given in this array of strings.
   */
   public $ErrorLines;
}

/**
 * @struct GetClubsRequest
 *
 * @brief Input parameters of the ::GetClubs API
 *
 * @see GetClubs
 * @ingroup TabTAPItypes
 * @version 0.7
 */
class GetClubsRequest {
  /**
   * Defines credentials to connect to a TabT website
   */
  public $Credentials;

  /**
   * The season when the players were members of the requested club
   *
   * This parameter is optional, default is the current season (see ::GetSeasons)
   */
  public $Season;

  /**
   * The identifier of the club category the returned clubs must belong to
   *
   * This parameter is optional, if not specified, all clubs will be returned
   *
   * @b type:  integer
   */
  public $ClubCategory;

  /**
   * The club to search for.
   *
   * This parameter contains the unique identifier of the club to be returned
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * This parameter is optional.
   *
   * @b type:  string
   */
  public $Club;
}

/**
 * @struct GetClubsResponse
 *
 * @brief Ouput parameters of the ::GetClubs API
 *
 * @see GetClubs
 * @ingroup TabTAPItypes
 */
class GetClubsResponse
{
  /**
   * The number of clubs returned by ::GetClubs
   *
   * This is also the number of entries that will be returned in ClubEntries
   */
  public $ClubCount;

  /**
   * List of all clubs matching the requested criteria (see ::GetClubs).
   *
   * Each element of the list is a ::ClubEntry
   *
   * @b type:  ClubEntry[]
   * @see ClubEntry
   */
  public $ClubsEntries;
}

/**
 * @struct ClubEntry
 *
 * @brief Information about a club
 *
 * This structure contains data related to a club, including a unique identified for the club
 * the name of the club, the category of the club, the number and details about venues where
 * the club is playing matches.
 *
 * @see GetClubs, GetClubsResponse
 * @ingroup TabTAPItypes
 * @version 0.7
 */
class ClubEntry
{
  /**
   * The unique index of the club.
   *
   * The index is usually given by the federation.  It can be a number or a string.
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW or <code>1305</code>
   * for Sporcrea club of Tecemo.
   *
   * @b type:  string
   */
  public $UniqueIndex;

  /**
   * The name of the club
   *
   * If a short name is available, it will be the short name.  A short name is convinient
   * to display a compact table (like for rankings).
   *
   * @b type:  string
   */
  public $Name;

  /**
   * The long name of the club
   *
   * A more formal or complete name of a club.  As an example, the Sporcrea club #2054
   * is called <code>TTG Mollem</code> but the complete name is <code>Tafeltennis
   * gezelschap Mollem</code>.
   *
   * @b type:  string
   */
  public $LongName;

  /**
   * The identifier of the club category the club belongs to
   *
   * Example: <code>2</code> for 
   *
   * @b type:  integer
   */
  public $Category;

  /**
   * The name of the club category the club belongs to
   *
   * @b type:  string
   */
  public $CategoryName;

  /**
   * The number of locations where the club can play matches
   *
   * This is also the number of entries that will be returned in VenueEntries
   *
   * @b type:  integer
   */
  public $VenueCount;

  /**
   * List of locations where a club can play a home match
   * Each element of the list is a ::VenueEntry
   *
   * Most of clubs have only one venue but some of them may have two or more venues.
   *
   * @b type:  VenueEntry[]
   * @see VenueEntry
   */
  public $VenueEntries;
}

/**
 * @struct VenueEntry
 *
 * @brief Information about a club venue 
 *
 * This structure contains data related to a place where matches are played.  Each club should
 * have at least one venue but can have multiples venues.
 *
 * @see GetClubs, GetClubsResponse
 * @ingroup TabTAPItypes
 * @version 0.7.6
 */
class VenueEntry
{
  /**
   * The unique identifier of the venue
   *
   * The system will generate a unique identifier for each venue.
   *
   * @b type:  integer
   */
  public $Id;

  /**
   * The identifier of the venue within the club
   *
   * Each club should have a least one venue.  The first venue of a club has always the
   * indentifier nummer 1.  This identifier is not unique accross all clubs.
   *
   * @b type:  integer
   */
  public $ClubVenue;

  /**
   * The name of the venue
   *
   * The venue can be a sport hall, a school, a building that is designated by a name
   *
   * @b type:  string
   */
  public $Name;

  /**
   * The street of the venue
   *
   * The name of street and the house number within the street.
   * Example: Seulestraat, 47
   *
   * @b type:  string
   */
  public $Street;

  /**
   * The town of the venue
   *
   * The name and the zip code of the town
   * Example: 8950 Nieuwekerke
   *
   * @b type:  string
   */
  public $Town;

  /**
   * The phone number of the venue
   *
   * A phone number that can be called if one has question about the venue
   *
   * @b type:  string
   */
  public $Phone;

  /**
   * A special comment about the venue
   *
   * Free text the championship organisator or venue owner can use to help people finding
   * the venue or letting them know special organisation issues.
   *
   * @b type:  string
   */
  public $Comment;

}


?>
