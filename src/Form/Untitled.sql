INSERT INTO `project`(`id`, `created_by_id`, `project_name`, `client_name`, `created_at`, `updated_at`) VALUES (NULL,(
    (
        SELECT user.id FROM user WHERE user.id = (
            INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`, `is_verified`, `is_subscribed`, `stripe_customer_id`, `stripe_subscription_id`, `address`, `city`, `state`, `postal_code`, `country`) VALUES (NULL, 'lll@mail.cam', '[]', 'password24', 'YOLO', 'POLO', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL)
        )
    )

),'test','test',NOW(),NULL)