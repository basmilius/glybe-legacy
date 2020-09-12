<?php
/*
 * Poll Class
 * Copyright © Bas Milius
 */
Class Poll
{
	public $Id;
	public $Vraag;
	public $Antwoorden;
	
	private $Gestemd;
	private $Votes;
	private $VotesForAnswer;
	
	public function __construct($id = 0, $topicId = 0)
	{
		if($id == 0)
		{
			throw new Exception("Poll ID kan niet 0 zijn!");
		}
		
		$this->Gestemd = Array();
		$this->Antwoorden = Array();
		
		$pQuery = DB::Query("SELECT * FROM `poll_questions` WHERE `id` = '" . $id . "' AND `topic_id` = '" . $topicId . "'");
		
		if(DB::NumRows($pQuery) === 0)
		{
			throw new Exception("Poll niet gevonden.");
		}
		
		$pFetch = DB::Fetch($pQuery);
		
		$this->Id = $pFetch['id'];
		$this->Vraag = $pFetch['question'];
		
		$aQuery = DB::Query("SELECT * FROM `poll_answers` WHERE `poll_id` = '" . $this->Id . "' ORDER BY `id`");
		while($aFetch = DB::Fetch($aQuery))
		{
			$this->Antwoorden[] = $aFetch;
			$this->VotesForAnswer[$aFetch['id']] = 0;
		}
		
		$vQuery = DB::Query("SELECT * FROM `poll_votes` WHERE `poll_id` = '" . $this->Id . "'");
		while($vFetch = DB::Fetch($vQuery))
		{
			$this->Gestemd[] = $vFetch['user_id'];
			$this->Votes[] = $vFetch;
			$this->VotesForAnswer[$vFetch['answer_id']]++;
		}
	}
	
	public function GetResultForAnswer($ansId = 0)
	{
		if($ansId == 0)
		{
			throw new Exception("Antwoord-ID mag niet 0 zijn!");
		}
		$percents = 0;
		if($this->VotesForAnswer[$ansId] > 0 && count($this->Votes) > 0)
		{
			$percents = (($this->VotesForAnswer[$ansId] / count($this->Votes)) * 100);
		}
		return Array($this->VotesForAnswer[$ansId], number_format($percents, 1, ",", ".")); // 1 = aantal stemmen; 2 = aantal procent van het totaal
	}
	
	public function GetDefaultStyledAnswer($answer = 0)
	{
		if($answer == 0)
		{
			throw new Exception("Antwoord mag niet 0 zijn!");
		}
		
		$result = $this->GetResultForAnswer($answer['id']);
		
		$return = '	<div class="poll_antwoord' . ((!$this->HasVoted()) ? ' clickable' : '') . '"' . ((!$this->HasVoted()) ? ' onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/poll_vote.php\', {pId:' . $this->Id . ',aId:' . $answer['id'] . ',sToken:\'' . sha1(md5($this->Id . $answer['id'])) . '\'});"' : '') . '><div class="answer">' . $answer['caption'] . '</div><div class="result"><div class="balk" style="width: ' . str_replace(",", ".", $result[1]) . '%;"></div><div class="caption">' . $result[0] . ' stemmen (' . $result[1] . '%)</div></div></div>';
		
		return $return;
	}
	
	public function HasVoted()
	{
		global $user;
		if(isset($user))
		{
			if(in_array($user->Id, $this->Gestemd))
			{
				return true; // Gebruiker heeft al gestemd!
			} else {
				return false; // Gebruiker heeft nog niet gestemd.
			}
		} else {
			return true; // Gasten mogen namelijk niet stemmen.
		}
	}
}
?>