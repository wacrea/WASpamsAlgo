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
	
	$total = count($comments);
	echo $total." rows fetched \n";

	$i = 0;

	$APIKey = '5adeec31c278';
	$BlogURL = 'http://bloglancome.isphers.com';
	$Akismet = new Akismet($BlogURL, $APIKey);
		

	foreach($comments AS $comment)
	{	
		$i++;

		$Akismet->setCommentAuthor($comment->comment_author);
		$Akismet->setCommentAuthorEmail($comment->comment_author_email);
		$Akismet->setCommentAuthorURL($comment->comment_author_url);
		$Akismet->setCommentContent($comment->comment_content);

		if($Akismet->isCommentSpam())
		{
			$connection->query("DELETE FROM wp_comments WHERE comment_ID = '".$comment->comment_ID."'");
			$connection->query("DELETE FROM wp_commentmeta WHERE comment_id = '".$comment->comment_ID."'");
			echo $i."/".$total." - SPAM \n";
		}
		else
		{
			echo $i."/".$total." - ok\n";
		}
	}
