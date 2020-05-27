<?php
/**
 * @defgroup TabTAPItypes TabT API type descriptions
 *
 * @brief Here are all types used by TabT API functions
 *
 * @author Gaëtan Frenoy <gaetan@frenoy.net>
 * @version 0.7.24
 *
 * Copyright (C) 2007-2020 Gaëtan Frenoy (gaetan@frenoy.net)
 */

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

  /**
   * Unique index of the member to be used when executing the current request.
   * 
   * For administrators, give them the option to send request on behalf of another user.
   * This can be interesting to make sure comments and other actions are recorded under the correct identity.
   *
   * @b type: int
   */
  public $OnBehalfOf;
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
   * @b type: ::CredentialsType
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
   * Default language is enforced by the configuration of the TabT instance but if
   * valid credentials are given, user's language will be used.  Hence, to change this language,
   * one has to connect to the TabT web interface and select his/her preferred language.
   *
   * If no default language is set by the TabT instance, English will be used.
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
   * The unique identifier of the division wherein the team is playing.
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
   * Divisions are grouped by category.  All divisions belonging to the same category are usually
   * sharing the same properties like the type of match (3 vs 3 or 4 vs 4) and the player ranking 
   * system (men or women).
   *
   * @b type:  int
   */
  public $DivisionCategory;

  /**
   * The type of games that are played during the match
   *
   * Each team match is played with a given number of single or double games that are played in
   * a given order.  The <i>MatchType</i> value defines how the games are played within a team match.
   *
   * As an example, for VTTL, MatchType #2 is played by 4 players who are playing 16 single games.
   *
   * @b type:  int
   */
  public $MatchType;
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
   * The number of individual games won by all players of the team
   *
   * @b type:  int
   */
  public $IndividualMatchesWon;

  /**
   * The number of individual games lost by all players of the team
   *
   * @b type:  int
   */
  public $IndividualMatchesLost;

  /**
   * The number of sets won by all players of the team during their individual games
   *
   * @b type:  int
   */
  public $IndividualSetsWon;

  /**
   * The number of sets won by all players of the team during their individual games
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

  /**
   * The club wherein the teams is playing.
   *
   * This parameter contains the unique identifier of the club where the team(s)
   * returned by ::GetClubTeams belongs to.
   * Example : <code>VLB-225</code> for VTTL club of Hurricane TTW.
   *
   * This parameter is usually not shown to the end user but can be used to easily find the club the team
   * is belonging to (easier that with the long name).
   *
   * @b type:  int
   */
  public $TeamClub;
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

  /**
   * Returns extended information about the member's new ranking evaluations.
   *
   * If set to "true", valid credential has to be specified.
   *
   * The number and level of details of the returned new ranking evaluations depend on the credential access rights and local
   * configuration option "option allow_own_ranking_info".
   *
   * This parameter is optional.  Default value is false.
   *
   * @b type:  boolean
   */
  public $RankingPointsInformation;

  /**
   * Returns results of the member for the selected season.
   *
   * This parameter is optional.  Default value is false.
   *
   * @b type:  boolean
   */
  public $WithResults;

  /**
   * Returns detailed information about the new ranking evaluations of each member's opponent.
   *
   * If set to "true", valid credential has to be specified.
   * This option requires administrative or ranking rights.
   *
   * This parameter is optional.  Default value is false.
   *
   * @b type:  boolean
   */
  public $WithOpponentRankingEvaluation;
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
   * The number of members returned by ::GetMembers
   *
   * This is also the number of entries that will be returned in ::MemberEntries
   *
   * @b type:  int
   */
  public $MemberCount;

  /**
   * List of all members matching the requested criteria (see ::GetMembersRequest).
   *
   * Each element of the list is a ::MemberEntryType
   *
   * @b type:  MemberEntryType[]
   * @see MemberEntryType
   */
  public $MemberEntries;
}

/**
 * @struct MemberEntryType
 *
 * @brief Describe a member
 *
 * @see GetMembers
 * @ingroup TabTAPItypes
 */
class MemberEntryType
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
   * Note: some members are not "playing members" so it means they cannot play any match.
   * Such players do not have a raking index
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

   /**
    * Number of entries in RankingPointsEntries
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * Note: NOT IMPLEMENTED YET
    *
    * @b type:  boolean
    */
  public $RankingPointsCount;

   /**
    * Information about the member's ranking points
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * Note: NOT IMPLEMENTED YET
    *
    * @b type:  boolean
    */
  public $RankingPointsEntries;

   /**
    * The player's main email address
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  string
    */
  public $Email;

   /**
    * The player's phone information
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  PhoneType
    */
  public $Phone;

   /**
    * The player's address
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  AddressType
    */
  public $Address;

   /**
    * The number of player's results
    *
    * This parameter is optional and only returned if WithResults has been set (see ::GetMembersRequest).
    *
    * @b type:  int
    */
  public $ResultCount;

   /**
    * Detailed player's results
    *
    * This parameter is optional and only returned if WithResults has been set (see ::GetMembersRequest).
    *
    * @b type:  PlayerResultEntry[]
    * @see PlayerResultEntry
    */
  public $ResultEntries;

   /**
    * The player's national number (if relevant)
    *
    * This parameter is optional and may not always be specified (see ::GetMembersRequest).
    *
    * @b type:  AddressType
    */
  public $NationalNumber;
}

/**
 * @struct PlayerResultEntry
 *
 * @brief A individual game result as returned by ::GetMembers
 *
 * @see GetMembers
 * @since Version 0.7.18
 * @ingroup TabTAPItypes
 */
class PlayerResultEntry
{
  /**
   * The date when the match has been played
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
   * The unique member index of the opponent
   *
   * @b type:  int
   * @see GetMembersResponse
   */
  public $UniqueIndex;

  /**
   * The given name of the opponent
   *
   * @b type:  string
   */
  public $FirstName;

  /**
   * The family name of the opponent
   *
   * @b type:  string
   */
  public $LastName;

  /**
   * The ranking of the opponent
   *
   * @b type:  string
   */
  public $Ranking;

  /**
   * The game result
   *  V = Victory of the player
   *  D = Defeat of the player
   *
   * @b type:  string
   */
  public $Result;
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
   * A division category is a logical group of divisions, usually by player type (men only, veterans, youth teams)
   *
   * @b type:  int
   */
   public $DivisionCategory;

   /**
   * The season when the matches have been played. 
   *
   * This parameter is optional, default is the current season (see ::GetSeasons).
   * Attention: if no season is given in conjuction with other parameters, the returned list of
   * matches may be empty because only matches of the current season will be considered
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

   /**
    * This paramter can be used to filter returned matches based on their date.
    * Only matches played after this date will be returned.
    *
    * Format of the date must be YYYY-MM-DD.
    *
    * This parameter is optional.  By default, no filter will be applied on the match date.
    *
    * @b type:  dateTime
    */
   public $YearDateFrom;

   /**
    * This paramter can be used to filter returned matches based on their date.
    * Only matches played before this date will be returned.
    *
    * Format of the date must be YYYY-MM-DD.
    *
    * This parameter is optional.  By default, no filter will be applied on the match date.
    *
    * @b type:  dateTime
    */
   public $YearDateTo;

   /**
    * Returns detailed results for all returned matches.  See ::TeamMatchEntry
    *
    * If set to "true", valid credential has to be specified.
    * Birthdate will only be given if given credential have administrive rights.
    *
    * This parameter is optional.  Default value is false.
    *
    * @b type:  boolean
    */
   public $WithDetails;

   /**
    * The match identifier to consider.
    *
    * This code is given by the championship organisator.  
    * Example: <code>VLB/01/002</code> or <code>LAND/12/121</code>.
    * This identifier is unique among all matches of a given season but may not be unique among all the matches.
    *
    * This parameter is optional.
    *
    * @b type:  string
    */
   public $MatchId;

   /**
    * The unique identified to consider.
    *
    * This code is given by the TabT application.  It is a positive integer that is unique among all matches.
    *
    * This parameter is optional.
    *
    * @b type:  int
    */
   public $MatchUniqueId;
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
  public $TeamMatchesEntries;
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
   * The unique identifier of the division wherein the match has been played.
   *
   * @b type:  int
   */
  public $DivisionId;

  /**
   * The category of the division wherein the match has been played.
   *
   * @b type:  int
   */
  public $DivisionCategory;

  /**
   * A identifier for the match
   *
   * This code is given by the championship organisator.  It is usually the combination of the week and a unique number within
   * the category.  Example: <code>PVLB/01/002</code> or <code>LAND/12/121</code>.
   * This identifier is unique among all matches of a given season but may not be unique among all the matches.
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
   * The club index of the venue where the match has to be played
   *
   * @b type:  string
   */
  public $VenueClub;

  /**
   * Details about the ::VenueEntry where the match has to be played
   *
   * @b type:  VenueEntry[]
   * @see VenueEntry
   */
  public $VenueEntry;

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

  /**
   * The name of the week (round) following the current one (given by WeekName)
   *
   * This is usually a number but can sometimes be a letter.
   * Example: <code>01</code>, <code>14</code>, <code>D</code>
   *
   * @b type:  string
   */
  public $NextWeekName;

  /**
   * The name of the week (round) preceding the current one (given by WeekName)
   *
   * This is usually a number but can sometimes be a letter.
   * Example: <code>01</code>, <code>14</code>, <code>D</code>
   *
   * @b type:  string
   */
  public $PreviousWeekName;

  /**
   * Indicates if the home team was not able to play the match and is forced to forteit the game.
   *
   * @b type:  boolean
   */
  public $IsHomeForfeited;

  /**
   * Indicates if the away team was not able to play the match and is forced to forteit the game.
   *
   * @b type:  boolean
   */
  public $IsAwayForfeited;

  /**
   * Indicates if the home team has completely withdrawn from the current competetion.
   * Possible values are:
   *  N: no (default)
   *  Y: yes
   *  1: yes, during the first part of the competition
   *  2: yes, during the second part of the competition
   *
   * @b type:  string
   */
  public $IsHomeWithdrawn;

  /**
   * Indicates if the away team has completely withdrawn from the current competetion.
   * Possible values are:
   *  N: no (default)
   *  Y: yes
   *  1: yes, during the first part of the competition
   *  2: yes, during the second part of the competition
   *
   * @b type:  string
   */
  public $IsAwayWithdrawn;

  /**
   * Indicates if the match has been validated.
   *
   * A match is typically locked when the match is validated by the division manager or when both teams have validated the match details.
   * 
   * @b type:  boolean
   */
  public $IsValidated;

  /**
   * Indicates if the match has been locked for modification.
   *
   * A match is typically locked when it has been validated (see IsValidated) or after a given amount of time without any further modifications.
   * 
   * @b type:  boolean
   */
  public $IsLocked;

  /**
   * Details about the team match (among others: when it has been played, who did played and the list of individual games)
   *
   * @b type:  TeamMatchDetailsEntry[]
   * @see TeamMatchDetailsEntry
   */
  public $MatchDetails;
}

/**
 * @struct TeamMatchDetailsEntry
 *
 * @brief Detailed informations about a match between two teams
 *
 * @see GetMatches, GetMatchesResponse
 * @since Version 0.7
 * @ingroup TabTAPItypes
 */
class TeamMatchDetailsEntry
{
  /**
   * Indicates if detailed information about a match between two team is available
   *
   * @b type:  boolean
   */
  public $DetailsCreated;

  /**
   * When the team match actually began
   *
   * @b type:  dateTime
   */
  public $StartTime;

  /**
   * When the team match actually ended
   *
   * @b type:  dateTime
   */
  public $EndTime;

  /**
   * The identifier of the member who was the captain of the home team
   *
   * @see UniqueIndex in ::GetMembersResponse
   *
   * @b type:  int
   */
  public $HomeCaptain;

  /**
   * The identifier of the member who was the captain of the away team
   *
   * @see UniqueIndex in ::GetMembersResponse
   *
   * @b type:  int
   */
  public $AwayCaptain;

  /**
   * The identifier of the member who was the referee of this team match
   *
   * @see UniqueIndex in ::GetMembersResponse
   *
   * @b type:  int
   */
  public $Referee;

  /**
   * The identifier of the member who was the hall commissioner of this team match
   *
   * @see UniqueIndex in ::GetMembersResponse
   *
   * @b type:  int
   */
  public $HallCommissioner;

  /**
   * List of players of the home team
   *
   * @b type:  TeamMatchPlayerList[]
   * @see TeamMatchPlayerList
   */
  public $HomePlayers;

  /**
   * List of players of the away team
   *
   * @b type:  TeamMatchPlayerList[]
   * @see TeamMatchPlayerList
   */
  public $AwayPlayers;

  /**
   * List of individual games that have been played during the team match
   *
   * @b type:  IndividualMatchResultEntry[]
   * @see IndividualMatchResultEntry
   */
  public $IndividualMatchResults;

  /**
   * Identifier of the match system used to play this team match
   *
   * see ::GetMatchSystems
   *
   * @b type:  int
   */
  public $MatchSystem;

  /**
   * Final score of the home team
   *
   * @b type:  int
   */
  public $HomeScore;

  /**
   * Final score of the away team
   *
   * @b type:  int
   */
  public $AwayScore;

  /**
   * The number of comments associated to this team match
   *
   * @b type:  integer
   */
  public $CommentCount;

  /**
   * List of comments associated to this team match.
   * Each element of the list is a ::CommentEntryType.
   *
   * Comments are only returned if a specific MatchUniqueId is given in ::GetMatches.
   *
   * @b type:  CommentEntryType[]
   * @see CommentEntryType
   */
  public $CommentEntries;
}

/**
 * @struct TeamMatchPlayerList
 *
 * @brief A list of players that are participating to a team match
 *
 * @see TeamMatchDetailsEntry
 * @since Version 0.7
 * @ingroup TabTAPItypes
 */
class TeamMatchPlayerList {
  /**
   * The number of single players of the (match) team
   *
   * @b type:  int
   */
  public $PlayerCount;

  /**
   * The number of double team of the (match) team
   *
   * @b type:  int
   */
  public $DoubleTeamCount;

  /**
   * The list of single players
   *
   * @b type:  TeamMatchPlayerEntry[]
   * @see TeamMatchPlayerEntryType
   */
  public $Players;

  /**
   * The list of double teams.
   *
   * @b type:  TeamMatchDoubleTeamEntry[]
   * @see TeamMatchDoubleTeamEntry
   */
  public $DoubleTeams;
}

/**
 * @struct TeamMatchPlayerEntry
 *
 * @brief Defines a single player of a team match
 *
 * @see TeamMatchPlayerList
 * @since Version 0.7
 * @ingroup TabTAPItypes
 */
class TeamMatchPlayerEntry {
  /**
   * What is the position of the player on the player list
   * (this can be important if the game list is predefined, see ::GetMatchSystems)
   *
   * @b type:  int
   */
  public $Position;

  /**
   * The unique member index of the player
   *
   * @b type:  int
   * @see GetMembersResponse
   */
  public $UniqueIndex;

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
   * The number of invidual games won by the player during the team match
   * Only games that have been actually played are counted (excluding forfeited games)
   *
   * @b type:  int
   */
  public $VictoryCount;

  /**
   * Indicates if the player forfeited all individual games of the team match
   *
   * If not defined, value is false.
   *
   * @b type:  boolean
   */
  public $IsForfeited;
}

/**
 * @struct TeamMatchDoubleTeamEntry
 *
 * @brief A double team of a team match
 *
 * @see TeamMatchPlayerList
 * @since Version 0.7
 * @ingroup TabTAPItypes
 */
class TeamMatchDoubleTeamEntry {
  /**
   * What is the position of the double team on the player list
   * (this can be important if the game list is predefined, see ::GetMatchSystems)
   *
   * @b type:  int
   */
  public $Position;

  /**
   * Defines the double team as a combination of the single players
   * The value depends on the number of single players
   * As an example, if there are 3 players in the single player list, the double teams can be:
   *  1 = Player 1 & Player 2
   *  2 = Player 1 & Player 3
   *  3 = Player 2 & Player 3
   *
   * @b type:  string
   */
  public $Team;

  /**
   * Indicates if the double team forfeited all double games of the team match
   *
   * If not defined, value is false.
   *
   * @b type:  boolean
   */
  public $IsForfeited;
}

/**
 * @struct IndividualMatchResultEntry
 *
 * @brief Result of a match between two players (aka single match) or team of two players (aka double match)
 *
 * @see IndividualMatchResultEntry
 * @since Version 0.7
 * @ingroup TabTAPItypes
 */
class IndividualMatchResultEntry {
  /**
   * What is the position of game during the team match
   *
   * @b type:  int
   */
  public $Position;

  /**
   * The index of the home player (defined as his/her position in the match player list, see TeamMatchPlayerEntry or TeamMatchDoubleTeamEntry)
   *
   * @b type:  int
   */
  public $HomePlayerMatchIndex;

  /**
   * The unique member index of the home player (see GetMembersRequest)
   *
   * @b type:  int
   */
  public $HomePlayerUniqueIndex;

  /**
   * The index of the away player (defined as his/her position in the match player list, see TeamMatchPlayerEntry or TeamMatchDoubleTeamEntry)
   *
   * @b type:  int
   */
  public $AwayPlayerMatchIndex;

  /**
   * The unique member index of the away player (see GetMembersRequest)
   *
   * @b type:  int
   */
  public $AwayPlayerUniqueIndex;

  /**
   * Number of sets won by the home player during the game
   *
   * @b type:  int
   */
  public $HomeSetCount;

  /**
   * Number of sets won by the away player during the game
   *
   * @b type:  int
   */
  public $AwaySetCount;

  /**
   * Indicates if the home player forfeited the game
   *
   * @b type:  boolean
   */
  public $IsHomeForfeited;

  /**
   * Indicates if the away player forfeited the game
   *
   * @b type:  boolean
   */
  public $IsAwayForfeited;

  /**
   * Detailed score of the individual game.
   * Each set score is separated by a slash (/)
   * A set score is the number of points scored by the set looser. If the away player wins the set, this number is preceded by a dash (-).
   * Example: if individual game scores are 11-7 / 4-11 / 11-8 / 11-5, score will be represented with 7/-4/8/5
   *
   * If no score has been registered (including when one of the players forfeited), this field is not returned.
   *
   * @b type:  string
   */
  public $Scores;
}

/**
 * @struct CommentEntryType
 *
 * @brief A comment on a team match
 * 
 * A comment can be automatically generated by the application (like when a match is confirmed) or can be a free text given by an application user.
 *
 * @see GetMatches
 * @since Version 0.7.22
 * @ingroup TabTAPItypes
 */
class CommentEntryType {
  /**
   * When the comment was recorded
   * 
   * @b type: DateTime
   */ 
  public $Timestamp;

  /**
   * Who was the author of that comment
   * 
   * @b type: MemberEntryType
   */ 
  public $Author;

  /**
   * The comment itself
   * 
   * Either a free text entered by an end user or an automatically generated comment (created by the application).
   * 
   * @b type: string
   */ 
  public $Comment;

  /**
   * The type of comments
   * 
   * The follow codes indicate the type of comment that is returned in this comment entry:
   *  DC = Automatic comment when the match sheet has been created
   *  DM = Automatic comment when match sheet has been modified
   *  DD = Automatic comment when match sheet has been deleted
   *  V  = Automatic comment when the match has been validated
   *  S  = Automatic comment when the team score of the match has been modified
   *  HW = Automatic comment when W-O status of the home team has been changed
   *  AW = Automatic comment when W-O status of the away team has been changed
   *  FF = Automatic comment when withdraw status of a team has been changed
   *  UC = User comment
   *  CD = Automatic comment when a user comment has been deleted
   * 
   * @b type: string
   */ 
  public $Code;
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
  public $ClubEntries;
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

/**
 * @struct GetTournamentsRequest
 *
 * @brief Input parameters of the ::GetTournaments API
 *
 * @see GetTournaments
 * @ingroup TabTAPItypes
 */
class GetTournamentsRequest {
  /**
   * Defines credentials (username and password) to connect to a TabT website
   *
   * @b type: ::CredentialsType
   */
  public $Credentials;

  /**
   * The season when the players were members of the requested club
   *
   * This parameter is optional, default is the current season (see ::GetSeasons)
   *
   * @b type:  int
   */
  public $Season;

  /**
   * The unique index of the tournament to consider.
   *
   * This parameter is optional.  If not given, all tournaments of the considered season will be returned.
   *
   * @b type:  int
   */
  public $TournamentUniqueIndex;

  /**
   * Returns results of the considered tournament.
   *
   * This parameter is optional.  Default value is false.
   * If set to true, a tournament must be selected using $TournamentUniqueIndex.
   *
   * @b type:  boolean
   */
  public $WithResults;

  /**
   * Returns registrations of the considered tournament.
   *
   * This parameter is optional.  Default value is false.
   * If set to true, a tournament must be selected using $TournamentUniqueIndex.
   *
   * @b type:  boolean
   */
  public $WithRegistrations;
}

/**
 * @struct GetTournamentsResponse
 *
 * @brief Output parameters of the ::GetTournaments API
 *
 * @see GetTournaments
 * @ingroup TabTAPItypes
 */
class GetTournamentsResponse {
  /**
   * The unique internal index of the tournament
   *
   * @b type:  int
   */
  public $UniqueIndex;

  /**
   * The name of the tournament
   *
   * @b type:  string
   */
  public $Name;

  /**
   * The level the tournament was played in.
   *
   * A level is a logical group of divisions (eg per provinces or regions).
   *
   * @b type:  int
   */
  public $Level;

  /**
   * The unique reference of the tournament
   *
   * @b type:  string
   */
  public $ExternalIndex;

  /**
   * When the tournament begins
   *
   * @b type:  date
   */
  public $DateFrom;

  /**
   * When the tournament ends?
   * 
   * This parameter is optional, if not given, the tournament ends the same day as it begins.
   *
   * @b type:  date
   */
  public $DateTo;

  /**
   * The date unti player can register for this tournament.
   *
   * @b type:  date
   */
  public $RegistrationDate;

  /**
   * The venue (location) where this tournament is played.
   *
   * @b type:  ::VenueEntry
   * @see VenueEntry
   */
  public $Venue;

  /**
   * The number of series played during this tournament.
   *
   * @b type:  int
   */
  public $SerieCount;

  /**
   * The list of series played during this tournament
   *
   * @b type: TournamentSerieEntryType[]
   * @see TournamentSerieEntryType
   */
  public $SerieEntries;
}

/**
 * @struct TournamentSerieEntryType
 *
 * @brief A tournament serie
 *
 * This structure contains data related to a serie that is played inside a tournament.
 *
 * @see GetTournaments
 * @since Version 0.7.16
 * @ingroup TabTAPItypes
 */
class TournamentSerieEntryType {
  /**
   * The unique internal index of the serie
   *
   * @b type:  int
   */
  public $UniqueIndex;

  /**
   * The name of the serie
   *
   * @b type:  string
   */
  public $Name;

   /**
    * The number of results for this serie
    *
    * This parameter is optional and only returned if WithResults has been set (see ::GetTournaments).
    *
    * @b type:  AddressType
    */
  public $ResultCount;

   /**
    * Detailed results of a match played within a tournament serie.
    *
    * This parameter is optional and only returned if WithResults has been set (see ::GetTournaments) and if some results are available for that serie.
    *
    * @b type:  IndividualMatchResultEntryType[]
    * @see IndividualMatchResultEntryType
    */
  public $ResultEntries;

  /**
    * The number of registrations for this serie
    *
    * This parameter is optional and only returned if WithRegistrations has been set (see ::GetTournaments).
    *
    * 
    */
  public $RegistrationCount;

  /**
    * Detailed registrations within a tournament serie.
    *
    * This parameter is optional and only returned if WithRegistrations has been set (see ::GetTournaments) 
    * and if some registrations are available for that serie.
    *
    * 
    */
  public $RegistrationEntries;
}

/**
 * @struct TournamentRegister
 *
 * @brief Input parameters of the ::TournamentRegister API
 *
 * @see TournamentRegister
 * @ingroup TabTAPItypes
 */
class TournamentRegister {
  /**
   * Defines credentials (username and password) to connect to a TabT website
   *
   * @b type: ::CredentialsType
   */
  public $Credentials;

  /**
   * The unique index of the tournament to which the player(s) want(s) to (un)register.
   *
   * This parameter is optional.  If not given, Tournament unique index of the serie will be taken.
   *
   * @b type:  int
   */
  public $TournamentUniqueIndex;

  /**
   * The unique index of the serie to which the player(s) want(s) to (un)register.
   *
   * @b type:  int
   */
  public $SerieUniqueIndex;

  /**
   * The unique index of the player to (un)register to/from the serie.
   * 
   * Two entries can be given (in case of double serie)
   *
   * @b type:  int
   */
  public $PlayerUniqueIndex;

  /**
   * If set to "true", player(s) will be unregistered from the serie.
   * 
   * This parameter is optional.  If not given, player(s) will be registered for the requested serie.
   * 
   * @b type:  boolean
   */
  public $Unregister;

  /**
   * If set to "no", concerned player will not receive a mail notification if their registration is changed.
   * 
   * This parameter is optional.  If not given, player(s) will be notified of any change in their tournament registration.
   * 
   * @b type:  boolean
   */
  public $NotifyPlayer;
}

/**
 * @struct TournamentRegisterResponse
 *
 * @brief Output parameters of the ::TournamentRegister API
 *
 * @see TournamentRegisterResponse
 * @ingroup TabTAPItypes
 */
class TournamentRegisterResponse {
  /**
   * true is the operation has been successfully performed.
   * false if the requested operation has not been executed (see MesssageEntries for more information about the reasons).
   *
   * @b type:  boolean
   */
  public $Success;

  /**
   * The number of messages returned by ::TournamentRegister.
   *
   * @b type:  int
   */
  public $MessageCount;

  /**
   * The list of message returned by ::TournamentRegister
   *
   * @b type:  String[]
   */
  public $MessageEntries;
}

?>
