<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/bootstrap.php';

if (!RA_ReadCookieCredentials($user, $points, $truePoints, $unreadMessageCount, $permissions, \RA\Permissions::Registered)) {
    //	Immediate redirect if we cannot validate user!	//TBD: pass args?
    header("Location: " . getenv('APP_URL'));
    exit;
}

$gameID = requestInputSanitized('g', null, 'integer');
$errorCode = requestInputSanitized('e');

$gameIDSpecified = (isset($gameID) && $gameID != 0);
$consoleName = null;
$gameIcon = null;
$gameTitle = null;
$hashes = null;
$forumTopicID = 0;
if ($gameIDSpecified) {
    getGameMetadata($gameID, $user, $achievementData, $gameData);
    $consoleName = $gameData['ConsoleName'];
    $consoleID = $gameData['ConsoleID'];
    $gameTitle = $gameData['Title'];
    $gameIcon = $gameData['ImageIcon'];
    $forumTopicID = $gameData['ForumTopicID'];
    $hashes = getHashListByGameID($gameID, true);
} else {
    //	Immediate redirect: this is pointless otherwise!
    header("Location: " . getenv('APP_URL'));
}

RenderHtmlStart();
RenderHtmlHead("Linked Hashes");
?>
<body>
<?php RenderTitleBar($user, $points, $truePoints, $unreadMessageCount, $errorCode, $permissions); ?>
<?php RenderToolbar($user, $permissions); ?>
<div id="mainpage">
    <div id='fullcontainer'>

        <h2>List of Linked Hashes</h2>

        <?php
        echo GetGameAndTooltipDiv($gameID, $gameTitle, $gameIcon, $consoleName, false, 96);
        echo "<br><br>";

        echo "<p><b>Hashes are used to confirm if two copies of a file are identical. " .
             "We use it to ensure the player is using the same ROM as the achievement developer, or a compatible one." .
             "<br/><br/>RetroAchievements only hashes portions of larger games to minimize load times, and strips " .
             "headers on smaller ones. Details on how the hash is generated for each system can be found " .
             "<a href='https://docs.retroachievements.org/Game-Identification/'>here</a>." .
             "</b></p>";

        echo "Currently this game has <b>" . count($hashes) . "</b> unique hashes registered for it:<br><br>";

        echo "<ul>";
        foreach ($hashes as $hash) {
            echo "<li>";
            echo "<code>  " . $hash['hash'] . "</code>";
            if (!empty($hash['User'])) {
                echo " linked by " . GetUserAndTooltipDiv($hash['User']);
            }
            echo "</li>";
        }
        echo "</ul>";

        echo "<br>";

        if ($forumTopicID > 0) {
            echo "Descriptions for these hashes may be listed on the <a href='viewtopic.php?t=$forumTopicID'>official forum topic</a>.<br/>";
        }

        ?>
        <br>
    </div>
</div>
<?php RenderFooter(); ?>
</body>
<?php RenderHtmlEnd(); ?>
