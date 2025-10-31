<?php //子テーマ用関数
if ( !defined( 'ABSPATH' ) ) exit;

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下に子テーマ用の関数を書く

///////////////////////////////////////////
// エントリーフォームのプルダウンショートコード
///////////////////////////////////////////
function my_job_posting_dropdown_shortcode() {
    ob_start();

    ?>
    <select name="job_postings" required="">
        <option value="">- 選択してください-</option>
        <?php
        // 表示したいカテゴリーのスラッグを配列で定義します
        // ★カテゴリーに増減があるときは、ここを編集してください★
        $categories = array('regular', 'part', 'newgraduate');
    
        foreach ( $categories as $category_slug ) {
            $category = get_category_by_slug( $category_slug );
    
            if ( $category ) {
                echo '<optgroup label="' . esc_attr( $category->name ) . '">';
    
                $args = array(
                    'post_type'      => 'post',
                    'category_name'  => $category_slug,
                    'posts_per_page' => -1,
                    'orderby'        => 'title',
                    'order'          => 'ASC'
                );
                
                $the_query = new WP_Query( $args );
                
                if ( $the_query->have_posts() ) {
                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
						echo '<option value="'.esc_html(get_the_title()).'">'.esc_html(get_the_title()).'</option>';
                    }
                    wp_reset_postdata();
                } else {
                    echo '<option disabled>現在募集はありません。</option>';
                }
    
                echo '</optgroup>';
            } else {
                echo '<option disabled>カテゴリーが見つかりませんでした: ' . esc_html($category_slug) . '</option>';
            }
        }
        ?>
    </select>
    <?php
    
    return ob_get_clean();
}

add_shortcode('job_dropdown', 'my_job_posting_dropdown_shortcode');
// エントリーフォームのプルダウンショートコードここまで