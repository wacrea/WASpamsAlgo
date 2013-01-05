<?php 
	//phpinfo();

	/*
	 * This algo works for Wordpress comments table
	 */

	require_once 'Akismet.class.php';
	echo "Akismet lib successfully loaded\n";

	// DB connect with PDO
	try {
	  $dns = 'mysql:host=localhost;dbname=WASpamsAlgo';
	  $user = 'root';
	  $pwd = 'root';
	  $connection = new PDO( $dns, $user, $pwd );
	  echo "MySQL PDO successful\n";
	} catch ( Exception $e ) {
	  echo "Error PDO MySQL : ", $e->getMessage();
	  die();
	}

	$select = $connection->query("SELECT * FROM wp_comments");
	$comments = $select->fetchAll(PDO::FETCH_OBJ);
	
	echo count($comments)." rows fetched \n";

	foreach($comments AS $comment)
	{
		$APIKey = 'your Akismet API key';
		$BlogURL = 'http://wordpress blog url';

		$Akismet = new Akismet($BlogURL ,$APIKey);
		$Akismet->setCommentAuthor($comment->comment_author);
		$Akismet->setCommentAuthorEmail($comment->comment_author_email);
		$Akismet->setCommentAuthorURL($comment->comment_author_url);
		$Akismet->setCommentContent($comment->comment_content);

		if($Akismet->isCommentSpam())
		{
			$connection->query("DELETE FROM wp_comments WHERE comment_ID = '".$comment->comment_ID."'");
			$connection->query("DELETE FROM wp_commentmeta WHERE comment_id = '".$comment->comment_ID."'");
			echo "SPAM \n";
		}
		else
		{
			echo "ok\n";
		}
	}