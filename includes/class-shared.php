<?php
/**
 * Podcast Blocks – shared code across entire plugin.
 *
 * Included by the core podcast-blocks.php for all page loads
 */

class Podcast_Blocks_Shared {

    /**
     * Return the array with nested children of all iTuens categories
     * 
     * Source: https://podcasters.apple.com/support/1691-apple-podcasts-categories
     */
    public static function get_itunes_categories() {
        return array(
            'Arts'                    => array( 'Books', 'Design', 'Fashion & Beauty', 'Food', 'Performing Arts', 'Visual Arts' ),
            'Business'                => array( 'Careers', 'Entrepreneurship', 'Investing', 'Management', 'Marketing', 'Non-Profit' ),
            'Comedy'                  => array( 'Comedy Interviews', 'Improv', 'Stand-Up' ),
            'Education'               => array( 'Courses', 'How To', 'Language Learning', 'Self-Improvement' ),
            'Fiction'                 => array( 'Comedy Fiction', 'Drama', 'Science Fiction' ),
            'Government'              => array(),
            'History'                 => array(),
            'Health & Fitness'        => array( 'Alternative Health', 'Fitness', 'Medicine', 'Mental Health', 'Nutrition', 'Sexuality' ),
            'Kids & Family'           => array( 'Education for Kids', 'Parenting', 'Pets & Animals', 'Stories for Kids' ),
            'Leisure'                 => array( 'Animation & Manga', 'Automotive', 'Aviation', 'Crafts', 'Games', 'Hobbies', 'Home & Garden', 'Video Games' ),
            'Music'                   => array( 'Music Commentary', 'Music History', 'Music Interviews' ),
            'News'                    => array( 'Business News', 'Daily News', 'Entertainment News', 'News Commentary', 'Politics', 'Sports News', 'Tech News' ),
            'Religion & Spirituality' => array( 'Buddhism', 'Christianity', 'Hinduism', 'Islam', 'Judaism', 'Religion', 'Spirituality' ),
            'Science'                 => array( 'Astronomy', 'Chemistry', 'Earth Sciences', 'Life Sciences', 'Mathematics', 'Natural Sciences', 'Nature', 'Physics', 'Social Sciences' ),
            'Society & Culture'       => array( 'Documentary', 'Personal Journals', 'Philosophy', 'Places & Travel', 'Relationships' ),
            'Sports'                  => array( 'Baseball', 'Basketball', 'Cricket', 'Fantasy Sports', 'Football', 'Golf', 'Hockey', 'Rugby', 'Running', 'Soccer', 'Swimming', 'Tennis', 'Track', 'Volleyball', 'Wilderness', 'Wrestling' ),
            'Technology'              => array(),
            'True Crime'              => array(),
            'TV & Film'               => array( 'After Shows', 'Film History', 'Film Interviews', 'Film Reviews', 'TV Reviews' ),
        );
    }

    /**
     * Return the array of categories as keys, with the string translation as the value
     */
    public static function get_itunes_categories_translated() {
        return array(
            // Primary categories
            'Arts'                    => __( 'Arts', 'podcast-blocks' ),
            'Business'                => __( 'Business', 'podcast-blocks' ),
            'Comedy'                  => __( 'Comedy', 'podcast-blocks' ),
            'Education'               => __( 'Education', 'podcast-blocks' ),
            'Fiction'                 => __( 'Fiction', 'podcast-blocks' ),
            'Government'              => __( 'Government', 'podcast-blocks' ),
            'History'                 => __( 'History', 'podcast-blocks' ),
            'Health & Fitness'        => __( 'Health & Fitness', 'podcast-blocks' ),
            'Kids & Family'           => __( 'Kids & Family', 'podcast-blocks' ),
            'Leisure'                 => __( 'Leisure', 'podcast-blocks' ),
            'Music'                   => __( 'Music', 'podcast-blocks' ),
            'News'                    => __( 'News', 'podcast-blocks' ),
            'Religion & Spirituality' => __( 'Religion & Spirituality', 'podcast-blocks' ),
            'Science'                 => __( 'Science', 'podcast-blocks' ),
            'Society & Culture'       => __( 'Society & Culture', 'podcast-blocks' ),
            'Sports'                  => __( 'Sports', 'podcast-blocks' ),
            'Technology'              => __( 'Technology', 'podcast-blocks' ),
            'True Crime'              => __( 'True Crime', 'podcast-blocks' ),
            'TV & Film'               => __( 'TV & Film', 'podcast-blocks' ),
            
            // Arts subcategories
            'Books'                   => __( 'Books', 'podcast-blocks' ),
            'Design'                  => __( 'Design', 'podcast-blocks' ),
            'Fashion & Beauty'        => __( 'Fashion & Beauty', 'podcast-blocks' ),
            'Food'                    => __( 'Food', 'podcast-blocks' ),
            'Performing Arts'         => __( 'Performing Arts', 'podcast-blocks' ),
            'Visual Arts'             => __( 'Visual Arts', 'podcast-blocks' ),
            
            // Business subcategories
            'Careers'                 => __( 'Careers', 'podcast-blocks' ),
            'Entrepreneurship'        => __( 'Entrepreneurship', 'podcast-blocks' ),
            'Investing'               => __( 'Investing', 'podcast-blocks' ),
            'Management'              => __( 'Management', 'podcast-blocks' ),
            'Marketing'               => __( 'Marketing', 'podcast-blocks' ),
            'Non-Profit'              => __( 'Non-Profit', 'podcast-blocks' ),
            
            // Comedy subcategories
            'Comedy Interviews'       => __( 'Comedy Interviews', 'podcast-blocks' ),
            'Improv'                  => __( 'Improv', 'podcast-blocks' ),
            'Stand-Up'                => __( 'Stand-Up', 'podcast-blocks' ),
            
            // Education subcategories
            'Courses'                 => __( 'Courses', 'podcast-blocks' ),
            'How To'                  => __( 'How To', 'podcast-blocks' ),
            'Language Learning'       => __( 'Language Learning', 'podcast-blocks' ),
            'Self-Improvement'        => __( 'Self-Improvement', 'podcast-blocks' ),
            
            // Fiction subcategories
            'Comedy Fiction'          => __( 'Comedy Fiction', 'podcast-blocks' ),
            'Drama'                   => __( 'Drama', 'podcast-blocks' ),
            'Science Fiction'         => __( 'Science Fiction', 'podcast-blocks' ),
            
            // Health & Fitness subcategories
            'Alternative Health'      => __( 'Alternative Health', 'podcast-blocks' ),
            'Fitness'                 => __( 'Fitness', 'podcast-blocks' ),
            'Medicine'                => __( 'Medicine', 'podcast-blocks' ),
            'Mental Health'           => __( 'Mental Health', 'podcast-blocks' ),
            'Nutrition'               => __( 'Nutrition', 'podcast-blocks' ),
            'Sexuality'               => __( 'Sexuality', 'podcast-blocks' ),
            
            // Kids & Family subcategories
            'Education for Kids'      => __( 'Education for Kids', 'podcast-blocks' ),
            'Parenting'               => __( 'Parenting', 'podcast-blocks' ),
            'Pets & Animals'          => __( 'Pets & Animals', 'podcast-blocks' ),
            'Stories for Kids'        => __( 'Stories for Kids', 'podcast-blocks' ),
            
            // Leisure subcategories
            'Animation & Manga'       => __( 'Animation & Manga', 'podcast-blocks' ),
            'Automotive'              => __( 'Automotive', 'podcast-blocks' ),
            'Aviation'                => __( 'Aviation', 'podcast-blocks' ),
            'Crafts'                  => __( 'Crafts', 'podcast-blocks' ),
            'Games'                   => __( 'Games', 'podcast-blocks' ),
            'Hobbies'                 => __( 'Hobbies', 'podcast-blocks' ),
            'Home & Garden'           => __( 'Home & Garden', 'podcast-blocks' ),
            'Video Games'             => __( 'Video Games', 'podcast-blocks' ),
            
            // Music subcategories
            'Music Commentary'        => __( 'Music Commentary', 'podcast-blocks' ),
            'Music History'           => __( 'Music History', 'podcast-blocks' ),
            'Music Interviews'        => __( 'Music Interviews', 'podcast-blocks' ),
            
            // News subcategories
            'Business News'           => __( 'Business News', 'podcast-blocks' ),
            'Daily News'              => __( 'Daily News', 'podcast-blocks' ),
            'Entertainment News'      => __( 'Entertainment News', 'podcast-blocks' ),
            'News Commentary'         => __( 'News Commentary', 'podcast-blocks' ),
            'Politics'                => __( 'Politics', 'podcast-blocks' ),
            'Sports News'             => __( 'Sports News', 'podcast-blocks' ),
            'Tech News'               => __( 'Tech News', 'podcast-blocks' ),
            
            // Religion & Spirituality subcategories
            'Buddhism'                => __( 'Buddhism', 'podcast-blocks' ),
            'Christianity'            => __( 'Christianity', 'podcast-blocks' ),
            'Hinduism'                => __( 'Hinduism', 'podcast-blocks' ),
            'Islam'                   => __( 'Islam', 'podcast-blocks' ),
            'Judaism'                 => __( 'Judaism', 'podcast-blocks' ),
            'Religion'                => __( 'Religion', 'podcast-blocks' ),
            'Spirituality'            => __( 'Spirituality', 'podcast-blocks' ),
            
            // Science subcategories
            'Astronomy'               => __( 'Astronomy', 'podcast-blocks' ),
            'Chemistry'               => __( 'Chemistry', 'podcast-blocks' ),
            'Earth Sciences'          => __( 'Earth Sciences', 'podcast-blocks' ),
            'Life Sciences'           => __( 'Life Sciences', 'podcast-blocks' ),
            'Mathematics'             => __( 'Mathematics', 'podcast-blocks' ),
            'Natural Sciences'        => __( 'Natural Sciences', 'podcast-blocks' ),
            'Nature'                  => __( 'Nature', 'podcast-blocks' ),
            'Physics'                 => __( 'Physics', 'podcast-blocks' ),
            'Social Sciences'         => __( 'Social Sciences', 'podcast-blocks' ),
            
            // Society & Culture subcategories
            'Documentary'             => __( 'Documentary', 'podcast-blocks' ),
            'Personal Journals'       => __( 'Personal Journals', 'podcast-blocks' ),
            'Philosophy'              => __( 'Philosophy', 'podcast-blocks' ),
            'Places & Travel'         => __( 'Places & Travel', 'podcast-blocks' ),
            'Relationships'           => __( 'Relationships', 'podcast-blocks' ),
            
            // Sports subcategories
            'Baseball'                => __( 'Baseball', 'podcast-blocks' ),
            'Basketball'              => __( 'Basketball', 'podcast-blocks' ),
            'Cricket'                 => __( 'Cricket', 'podcast-blocks' ),
            'Fantasy Sports'          => __( 'Fantasy Sports', 'podcast-blocks' ),
            'Football'                => __( 'American Football', 'podcast-blocks' ),
            'Golf'                    => __( 'Golf', 'podcast-blocks' ),
            'Hockey'                  => __( 'Hockey', 'podcast-blocks' ),
            'Rugby'                   => __( 'Rugby', 'podcast-blocks' ),
            'Running'                 => __( 'Running', 'podcast-blocks' ),
            'Soccer'                  => __( 'Football (Soccer)', 'podcast-blocks' ),
            'Swimming'                => __( 'Swimming', 'podcast-blocks' ),
            'Tennis'                  => __( 'Tennis', 'podcast-blocks' ),
            'Track'                   => __( 'Track', 'podcast-blocks' ),
            'Volleyball'              => __( 'Volleyball', 'podcast-blocks' ),
            'Wilderness'              => __( 'Wilderness', 'podcast-blocks' ),
            'Wrestling'               => __( 'Wrestling', 'podcast-blocks' ),
            
            // TV & Film subcategories
            'After Shows'             => __( 'After Shows', 'podcast-blocks' ),
            'Film History'            => __( 'Film History', 'podcast-blocks' ),
            'Film Interviews'         => __( 'Film Interviews', 'podcast-blocks' ),
            'Film Reviews'            => __( 'Film Reviews', 'podcast-blocks' ),
            'TV Reviews'              => __( 'TV Reviews', 'podcast-blocks' ),
        );
    }

    /**
     * Returns the inline SVG icon for a podcast service.
     *
     * @param string $service_id  One of: apple, spotify, audible, youtube, rss.
     * @return string             SVG markup string, or empty string if unknown.
     */
    public static function service_icon( $service_id ) {
        $icons = array(

            // Apple Podcasts – purple rounded square, microphone symbol.
            'apple' => '<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                . '<rect width="50" height="50" rx="11" fill="#B150E2"/>'
                . '<rect x="19" y="10" width="12" height="18" rx="6" fill="white"/>'
                . '<path d="M13 25c0 8 5 13 12 13s12-5 12-13" fill="none" stroke="white" stroke-width="3.2" stroke-linecap="round"/>'
                . '<line x1="25" y1="38" x2="25" y2="43" stroke="white" stroke-width="3.2" stroke-linecap="round"/>'
                . '<line x1="19" y1="43" x2="31" y2="43" stroke="white" stroke-width="3.2" stroke-linecap="round"/>'
                . '</svg>',

            // Spotify – green circle, three curved equaliser lines.
            'spotify' => '<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                . '<circle cx="25" cy="25" r="25" fill="#1DB954"/>'
                . '<path d="M12 19c9-5 17-5 26 0" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round"/>'
                . '<path d="M14 27c8-4 14-4 22 0" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"/>'
                . '<path d="M16 34c7-3 11-3 18 0" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"/>'
                . '</svg>',

            // Audible – orange rounded square, headphone icon.
            'audible' => '<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                . '<rect width="50" height="50" rx="11" fill="#FF9900"/>'
                . '<path d="M11 27v-5a14 14 0 0 1 28 0v5" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round"/>'
                . '<rect x="7" y="26" width="9" height="13" rx="3.5" fill="white"/>'
                . '<rect x="34" y="26" width="9" height="13" rx="3.5" fill="white"/>'
                . '</svg>',

            // YouTube – red rounded square, play triangle.
            'youtube' => '<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                . '<rect width="50" height="50" rx="11" fill="#FF0000"/>'
                . '<polygon points="19,14 38,25 19,36" fill="white"/>'
                . '</svg>',

            // RSS – orange rounded square, broadcast arcs + dot.
            'rss' => '<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                . '<rect width="50" height="50" rx="11" fill="#F26522"/>'
                . '<circle cx="13" cy="38" r="4.5" fill="white"/>'
                . '<path d="M13 27q10 0 10 11" fill="none" stroke="white" stroke-width="4" stroke-linecap="round"/>'
                . '<path d="M13 17q20 0 20 21" fill="none" stroke="white" stroke-width="4" stroke-linecap="round"/>'
                . '</svg>',
            
            // Close - close x icon, for closing modals
            'close' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" '
                . 'fill="currentColor" aria-hidden="true">'
                . '<path d="M2.47 2.47a.75.75 0 0 1 1.06 0L8 6.94l4.47-4.47a.75.75 0 1 1 1.06 1.06'
                . 'L9.06 8l4.47 4.47a.75.75 0 1 1-1.06 1.06L8 9.06l-4.47 4.47a.75.75 0 0 1-1.06-1.06'
                . 'L6.94 8 2.47 3.53a.75.75 0 0 1 0-1.06z"/>'
                . '</svg>',
        );

        return isset( $icons[ $service_id ] ) ? $icons[ $service_id ] : '';
    }
}

// eof