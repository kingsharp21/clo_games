<?php

$page_title = 'Leaderboard';
$meta_description = 'Top players';

require_once( TEMPLATE_PATH . '/functions.php' ); //Load theme functions

include  TEMPLATE_PATH . "/includes/header.php";

// CONTENT
?>

    <div class="container">
        <div class="game-container">
            <?php

            $amount = 10;

            $conn = open_connection();
            $sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER by score DESC LIMIT ".$amount;
            $st = $conn->prepare($sql);
            $st->execute();
            //
            $row = $st->fetchAll(PDO::FETCH_ASSOC);
            $list = [];
            foreach($row as $item){
                $game = Game::getById($item['game_id']);
                if($game){
                    $item['game_title'] = $game->title;
                    $item['username'] = User::getById($item['user_id'])->username;
                    array_push($list, $item);
                }
            }

            //Show the list
            ?>

            <h3>Top Players</h3>
            <br>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Usename</th>
                    <th scope="col">Game Title</th>
                    <th scope="col">Score</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $index = 0;
                foreach($list as $item){
                    $index++;
                    ?>

                    <tr>
                        <th scope="row"><?php echo $index ?></th>
                        <td><?php echo $item['username'] ?></td>
                        <td><?php echo $item['game_title'] ?></td>
                        <td><?php echo $item['score'] ?></td>
                    </tr>

                    <?php
                }

                ?>
                </tbody>
            </table>
        </div>
    </div>

<?php

include  TEMPLATE_PATH . "/includes/footer.php"

?>