<?php
if ( !defined( 'MEDIAWIKI' ) ) {
    exit;
}

$wgExtensionCredits['hiddensource'][] = array(
    'path' => __FILE__,
    'name' => 'HiddenSource',
    'author' => array( 'Galen Han' ),
    'url' => 'http://galenhan.com',
    'version' => '1.0',
    'descriptionmsg' => 'Hides the source code of articles from users that are not logged in. '
);

$wgHooks['MediaWikiPerformAction'][] = 'HiddenSource';

function HiddenSource( $output, $article, $title, $user, $request ) {
    global $wgUser;

    //If they are not logged in
    if ( !$wgUser->isLoggedIn() ) {
        $action = $request->getVal( 'action' );

        // these actions will either reveal source information about the article, 
        // or are actions that should not be executable if a user is not allowed to edit an article.
        $blockedactions = array('raw', 'delete', 'revert', 'rollback', 'protect', 'unprotect', 
        'markpatrolled', 'deletetrackback', 'edit', 'blockdiff', 'submit');
        
        // here's where the real action will take place....
        if(in_array($action, $blockedactions)) {
            // sets the page title to the title you want displayed to the user.
            $output->setPageTitle( 'Please log in to view the source' );
            // this sets the title bar to the standard MediaWiki "Error" title for error pages.  
            $output->setHTMLTitle( wfMessage( 'errorpagetitle' )->text() );
            // this is for spiders, like GoogleBot, etc. telling them not to index this page
            $output->setRobotPolicy( 'noindex,nofollow' );
            // indicates that this is not related to an article
            $output->setArticleRelated( false );
            // turns off caching for this page
            $output->enableClientCache( false );
            // blanks out any redirect instructions
            $output->mRedirect = '';
            // blanks out any article text that might have been previously loaded
            $output->mBodytext = '';
            // add a link to return to the article's view page
            $output->addReturnTo( $title );
            return false;
        }
    }
}
