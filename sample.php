<?php

include_once("lib/entities.php");

// $association = Association::listAssociations(array("limit" => 1));

// $association = $association[0];

// $associationDue = new AssociationDue();
// $associationDue->association_id = $association->id;
// $associationDue->dues = 18.02;
// $associationDue->added_by = 0;

// print var_dump($associationDue);

// print "=> ".$associationDue->save()." <=";

//$associationDue = AssociationDue::findAssociationDue(1);
$member = Member::listMembers();
$member = $member[0];
print var_dump($member);
$memberAssociation = MemberAssociation::listMemberAssociations(array("member_id" => $member->id, 
	"index" => 0 , "limit" => 100));
$memberAssociation = $memberAssociation[0];
print var_dump($memberAssociation);

$associationDue = AssociationDue::findAssociationDue($memberAssociation->association_id);
print $associationDue;
$memberAssociationDue = new MemberAssociationDue();
$memberAssociationDue->member_id = $member->id;
$memberAssociationDue->association_id = $memberAssociation->id;
$memberAssociationDue->dues = $associationDue->dues;
$memberAssociationDue->dues = date("m");
print " => ".$memberAssociationDue->save();
// print var_dump($associationDue);