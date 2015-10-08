<?php
namespace br\gov\sial\core\util\lib\PHPMailer\exception;

/*~ PHPMailerException.php
.---------------------------------------------------------------------------.
|  Software: PHPMailer - PHP email class                                    |
|   Version: 5.1                                                            |
|   Contact: via sourceforge.net support pages (also www.worxware.com)      |
|      Info: http://phpmailer.sourceforge.net                               |
|   Support: http://sourceforge.net/projects/phpmailer/                     |
| ------------------------------------------------------------------------- |
|     Admin: Andy Prevost (project admininistrator)                         |
|   Authors: Andy Prevost (codeworxtech) codeworxtech@users.sourceforge.net |
|          : Marcus Bointon (coolbru) coolbru@users.sourceforge.net         |
|   Founder: Brent R. Matzelle (original founder)                           |
| Copyright (c) 2004-2009, Andy Prevost. All Rights Reserved.               |
| Copyright (c) 2001-2003, Brent R. Matzelle                                |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Lesser General Public License (LGPL)     |
|            http://www.gnu.org/copyleft/lesser.html                        |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
| ------------------------------------------------------------------------- |
| We offer a number of paid services (www.worxware.com):                    |
| - Web Hosting on highly optimized fast and secure servers                 |
| - Technology Consulting                                                   |
| - Oursourcing (highly qualified programmers and graphic designers)        |
'---------------------------------------------------------------------------'
*/

/**
 * PHPMailer - PHP email transport class
 * NOTE: Requires PHP version 5 or later
 * @package PHPMailerException
 * @author Andy Prevost
 * @author Marcus Bointon
 * @copyright 2004 - 2009 Andy Prevost
 * @version $Id: PHPMailerException.php 5138 2012-06-26 19:53:21Z bruno.menezes $
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
class PHPMailerException extends \Exception
{
  public function errorMessage()
  {
    $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
    return $errorMsg;
  }
}