<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$message              = $this->message;
$topic                = $message->getTopic();
$category             = $message->getCategory();
$isReply              = $this->message->id != $this->topic->first_post_id;
$signature            = $this->profile->getSignature();
$attachments          = $message->getAttachments();
$avatarname           = $this->profile->getname();
$attachs              = $message->getNbAttachments();
$topicStarter         = $this->topic->first_post_userid == $this->message->userid;
$config               = KunenaConfig::getInstance();
$subjectlengthmessage = $this->ktemplate->params->get('SubjectLengthMessage', 20);

if ($config->ordering_system == 'mesid')
{
	$this->numLink = $this->location;
}
else
{
	$this->numLink = $message->replynum;
}
?>

	<small class="text-muted pull-right">
		<?php if ($this->ipLink && !empty($this->message->ip)) : ?>
			<?php echo KunenaIcons::ip(); ?>
			<span class="ip"> <?php echo $this->ipLink; ?> </span>
		<?php endif; ?>
		<?php echo KunenaIcons::clock(); ?>
		<?php echo $message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
		<?php if ($message->modified_time) : ?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); endif; ?>
		<a href="#<?php echo $this->message->id; ?>" id="<?php echo $this->message->id; ?>"
		   rel="canonical">#<?php echo $this->numLink; ?></a>
		<span class="visible-phone"><?php echo Text::_('COM_KUNENA_BY') . ' ' . $message->getAuthor()->getLink(); ?></span>
	</small>
	<div class="horizontal-message">
		<div class="profile-horizontal-top">
			<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('horizontal')->set('topic_starter', $topicStarter)->set('category_id', $this->category->id); ?>
		</div>
		<div class="badger-left badger-info <?php if ($message->getAuthor()->isModerator()) : ?> badger-moderator <?php endif; ?> message-<?php echo $this->message->getState(); ?>">
			<div class="mykmsg-header">
				<?php
				$title   = KunenaForumMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage);
				$langstr = $isReply ? 'COM_KUNENA_MESSAGE_REPLIED_NEW' : 'COM_KUNENA_MESSAGE_CREATED_NEW';
				echo Text::sprintf($langstr, $message->getAuthor()->getLink(), $this->getTopicLink($topic, 'first', null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false)); ?>
			</div>
			<div class="kmsg">
				<?php if (!$this->me->userid && !$isReply) :
					echo $message->displayField('message');
				else:
					echo (!$this->me->userid && $this->config->teaser) ? Text::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
				endif; ?>
			</div>

			<?php if ($signature) : ?>
				<div class="ksig">
					<hr>
					<span class="ksignature"><?php echo $signature; ?></span>
				</div>
			<?php endif ?>
		</div>
	</div>

<?php if ($this->config->reportmsg && $this->me->exists()) :
	echo KunenaLayout::factory('Widget/Button')
		->setProperties(array('url'   => '#report' . $message->id . '', 'name' => 'report', 'scope' => 'message',
		                      'type'  => 'user', 'id' => 'btn_report', 'normal' => '', 'icon' => KunenaIcons::reportname(),
		                      'modal' => 'modal', 'pullright' => 'pullright', ));
	if ($this->me->isModerator($this->topic->getCategory()) || $this->config->user_report || !$this->config->user_report && $this->me->userid != $this->message->userid) : ?>
		<div id="report<?php echo $this->message->id; ?>" class="modal hide fade" tabindex="-1" role="dialog"
		     aria-hidden="true" data-backdrop="false">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id); ?>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($attachments)) : ?>
	<div class="kattach">
		<h5> <?php echo Text::_('COM_KUNENA_ATTACHMENTS'); ?> </h5>
		<ul class="thumbnails">
			<?php foreach ($attachments as $attachment) : ?>
				<?php if ($attachment->isAudio()) :
					echo $attachment->getLayout()->render('audio'); ?>
				<?php elseif ($attachment->isVideo()) :
					echo $attachment->getLayout()->render('video'); ?>
				<?php else : ?>
					<li class="span3 center">
						<div
								class="thumbnail"> <?php echo $attachment->getLayout()->render('thumbnail'); ?><?php echo $attachment->getLayout()->render('textlink'); ?> </div>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
<?php elseif ($attachs->total > 0 && !$this->me->exists()) :
	if ($attachs->image > 0 && !$this->config->showimgforguest)
	{
		if ($attachs->image > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_SIMPLE'))->setLayout('unauthorised');
		}
	}

	if ($attachs->file > 0 && !$this->config->showfileforguest)
	{
		if ($attachs->file > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_SIMPLE'))->setLayout('unauthorised');
		}
	}
endif; ?>
<?php if ($message->modified_by && $this->config->editmarkup) :
	$dateshown = $datehover = '';

	if ($message->modified_time)
	{
		$datehover = 'title="' . KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat_hover') . '"';
		$dateshown = KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat') . ' ';
	} ?>
	<div class="alert alert-info hidden-phone" <?php echo $datehover ?>>
		<?php echo Text::sprintf('COM_KUNENA_EDITING_LASTEDIT_ON_BY', $dateshown, $message->getModifier()->getLink(null, null, '', '', null, $this->category->id)); ?>
		<?php

		if ($message->modified_reason)
		{
			echo Text::_('COM_KUNENA_REASON') . ': ' . $this->escape($message->modified_reason);
		} ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->thankyou)) : ?>
	<div class="kmessage-thankyou">
		<?php
		echo Text::_('COM_KUNENA_THANKYOU') . ': ' . implode(', ', $this->thankyou) . ' ';

		if ($this->more_thankyou)
		{
			echo Text::sprintf('COM_KUNENA_THANKYOU_MORE_USERS', $this->more_thankyou);
		}
		?>
	</div>
<?php endif;
