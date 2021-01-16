<?php
//http://stackoverflow.com/questions/10073425/is-there-a-way-to-get-the-list-of-available-locales-in-php
header( 'Content-Type: text/html; charset=utf-8' );
// source of the list:
// http://msdn.microsoft.com/en-us/library/39cwe7zf(v=vs.90).aspx
$langs = array(
    // language, sublanguage, codes
    array( 'Chinese', 'Chinese', array( 'chinese' ) ),
    array( 'Chinese', 'Chinese (simplified)', array( 'chinese-simplified', 'chs' ) ),
    array( 'Chinese', 'Chinese (traditional)', array( 'chinese-traditional', 'cht' ) ),
    array( 'Czech', 'Czech', array( 'csy', 'czech' ) ),
    array( 'Danish', 'Danish', array( 'dan', 'danish' ) ),
    array( 'Dutch', 'Dutch (default)', array( 'dutch', 'nld' ) ),
    array( 'Dutch', 'Dutch (Belgium)', array( 'belgian', 'dutch-belgian', 'nlb' ) ),
    array( 'English', 'English (default)', array( 'english' ) ),
    array( 'English', 'English (Australia)', array( 'australian', 'ena', 'english-aus' ) ),
    array( 'English', 'English (Canada)', array( 'canadian', 'enc', 'english-can' ) ),
    array( 'English', 'English (New Zealand)', array( 'english-nz', 'enz' ) ),
    array( 'English', 'English (United Kingdom)', array( 'eng', 'english-uk', 'uk' ) ),
    array( 'English', 'English (United States)', array( 'american', 'american english', 'american-english', 'english-american', 'english-us', 'english-usa', 'enu', 'us', 'usa' ) ),
    array( 'Finnish', 'Finnish', array( 'fin', 'finnish' ) ),
    array( 'French', 'French (default)', array( 'fra', 'french' ) ),
    array( 'French', 'French (Belgium)', array( 'frb', 'french-belgian' ) ),
    array( 'French', 'French (Canada)', array( 'frc', 'french-canadian' ) ),
    array( 'French', 'French (Switzerland)', array( 'french-swiss', 'frs' ) ),
    array( 'German', 'German (default)', array( 'deu', 'german' ) ),
    array( 'German', 'German (Austria)', array( 'dea', 'german-austrian' ) ),
    array( 'German', 'German (Switzerland)', array( 'des', 'german-swiss', 'swiss' ) ),
    array( 'Greek', 'Greek', array( 'ell', 'greek' ) ),
    array( 'Hungarian', 'Hungarian', array( 'hun', 'hungarian' ) ),
    array( 'Icelandic', 'Icelandic', array( 'icelandic', 'isl' ) ),
    array( 'Italian', 'Italian (default)', array( 'ita', 'italian' ) ),
    array( 'Italian', 'Italian (Switzerland)', array( 'italian-swiss', 'its' ) ),
    array( 'Japanese', 'Japanese', array( 'japanese', 'jpn' ) ),
    array( 'Korean', 'Korean', array( 'kor', 'korean' ) ),
    array( 'Norwegian', 'Norwegian (default)', array( 'norwegian' ) ),
    array( 'Norwegian', 'Norwegian (Bokmal)', array( 'nor', 'norwegian-bokmal' ) ),
    array( 'Norwegian', 'Norwegian (Nynorsk)', array( 'non', 'norwegian-nynorsk' ) ),
    array( 'Polish', 'Polish', array( 'plk', 'polish' ) ),
    array( 'Portuguese', 'Portuguese (default)', array( 'portuguese', 'ptg' ) ),
    array( 'Portuguese', 'Portuguese (Brazil)', array( 'portuguese-brazilian', 'ptb' ) ),
    array( 'Russian', 'Russian (default)', array( 'rus', 'russian' ) ),
    array( 'Slovak', 'Slovak', array( 'sky', 'slovak' ) ),
    array( 'Spanish', 'Spanish (default)', array( 'esp', 'spanish' ) ),
    array( 'Spanish', 'Spanish (Mexico)', array( 'esm', 'spanish-mexican' ) ),
    array( 'Spanish', 'Spanish (Modern)', array( 'esn', 'spanish-modern' ) ),
    array( 'Swedish', 'Swedish', array( 'sve', 'swedish' ) ),
    array( 'Turkish', 'Turkish', array( 'trk', 'turkish' ) )
);
echo '<table>'."\n";
echo '<tr>'."\n";
echo '  <th>Languange</th>'."\n";
echo '  <th>Sub-Languange</th>'."\n";
echo '  <th>Languange String</th>'."\n";
echo '</tr>'."\n";
foreach ( $langs as $lang ) {
    echo '<tr>'."\n";
    echo '  <td>'.$lang[0].'</td>'."\n";
    echo '  <td>'.$lang[1].'</td>'."\n";
    $a = array();
    foreach ( $lang[2] as $lang_code ) {
        $loc = setlocale( LC_ALL, $lang_code );
        $a []= $lang_code.' '.( false === $loc ? '?' : '? - '.$loc );
    }
    echo '  <td>'.implode( '<br>', $a ).'</td>'."\n";
    echo '</tr>'."\n";
}
echo '</table>'."\n";
// Note: Norvegian (Bokmal) is Norvegian (Bokmål), see: http://en.wikipedia.org/wiki/Bokmål
?>