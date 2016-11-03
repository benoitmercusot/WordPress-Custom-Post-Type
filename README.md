# WordPress-Custom-Post-Type

Création rapide et facile d'un "Custom Post Type" avec création dynamique d'une page de réglages qui vous donne la possibilité de modifier le titre de l'archive, la description de l'archive et d'ajouter une image d'archive. 

Ajouter également au back-office la possibilité de gérer le nombre de post par page et l'ordre d'affichage des posts.


## Quickstart.

1 - S'utilise et s'installe sous forme de plugin ou de mu-plugin.

2 - Ajuster les variables suivantes 

protected $_post_type = 'portfolio';

protected $_post_type_name = 'Réalisation';

protected $_post_type_archive_slug = 'portfolio';

protected $_post_type_icon = 'dashicons-lightbulb';

protected $_post_type_menu_position = 18;

protected $_post_type_supports = array('title','editor','thumbnail','excerpt','custom-fields');

3 - Dans votre thème, ajouter les actions suivantes dans votre fichier "archive-$_post_type.php"

do_action('portfolio_archive_title'); ==> Pour afficher le titre

do_action('portfolio_archive_description'); ==> Pour afficher la description

do_action('portfolio_archive_thumbnail'); ==> Pour afficher l'illustration

<a href="https://www.mbcreation.net">MB Création</a>