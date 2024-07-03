
---Updating znote forum tables if you already have znote forum.

ALTER TABLE znote_forum
ADD category_comment VARCHAR (225) NOT NULL default '';

ALTER TABLE znote_forum_posts
ADD forum_id INT NOT NULL default '0';


