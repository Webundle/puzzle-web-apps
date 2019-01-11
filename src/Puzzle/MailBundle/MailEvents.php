<?php

namespace Puzzle\NewsletterBundle;

final class MailEvents {
	/**
	 * Send mail to new susbcriber
	 * @var string
	 */
	const NEW_SUBSCRIBER = 'newsletter.add_subscriber';

	/**
	 * Send mail to admin when new contact message is sent
	 * @var string
	 */
	const NEW_CONTACT = 'newsletter.create_contact';
	
}