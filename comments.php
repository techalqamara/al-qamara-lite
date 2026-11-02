<?php
/**
 * Comments Template
 *
 * @package alqamara
 */

// Do not allow direct access
if ( post_password_required() ) {
    echo '<p class="comment-password-required">' . esc_html__( 'This post is password protected. Enter the password to view comments.', 'al-qamara-lite' ) . '</p>';
    return;
}
?>

<!-- ===================== COMMENTS SECTION ===================== -->
<div class="comments-section mt-5">


    <?php if ( have_comments() ) : ?>

    <!-- ===================== COMMENTS LIST ===================== -->
    <div class="comments-list-wrap">

        <h2 class="comments-heading">
            <i class="fas fa-comments me-2 text-primary" aria-hidden="true"></i>
            <?php
            printf(
                /* translators: %d: number of comments */
                esc_html( _n(
                    '%d Comment',
                    '%d Comments',
                    get_comments_number(),
                    'al-qamara-lite'
                ) ),
                esc_html( number_format_i18n( get_comments_number() ) )
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( [
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 56,
                'callback'    => 'alqamara_comment_template',
            ] );
            ?>
        </ol>

        <!-- Comments Pagination -->
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
        <nav class="comment-navigation d-flex justify-content-between mt-4" aria-label="<?php esc_attr_e( 'Comment navigation', 'al-qamara-lite' ); ?>">
            <div class="nav-previous">
                <?php previous_comments_link( '<i class="fas fa-arrow-left me-2"></i>' . esc_html__( 'Older Comments', 'al-qamara-lite' ) ); ?>
            </div>
            <div class="nav-next">
                <?php next_comments_link( esc_html__( 'Newer Comments', 'al-qamara-lite' ) . '<i class="fas fa-arrow-right ms-2"></i>' ); ?>
            </div>
        </nav>
        <?php endif; ?>

    </div>
    <!-- ===================== COMMENTS LIST END ===================== -->

    <?php endif; ?>


    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
    <!-- Comments Closed -->
    <p class="comments-closed">
        <i class="fas fa-lock me-2" aria-hidden="true"></i>
        <?php esc_html_e( 'Comments are closed.', 'al-qamara-lite' ); ?>
    </p>
    <?php endif; ?>


    <?php
    // ===================== COMMENT FORM =====================
    $alqamara_req = (bool) get_option( 'require_name_email' );
    comment_form( [
        'title_reply'          => esc_html__( 'Leave a Comment',          'al-qamara-lite' ),
        /* translators: %s: name of the comment author being replied to */
        'title_reply_to'       => esc_html__( 'Reply to %s',              'al-qamara-lite' ),
        'title_reply_before'   => '<h2 class="comments-heading"><i class="fas fa-pen me-2 text-primary" aria-hidden="true"></i>',
        'title_reply_after'    => '</h2>',
        'cancel_reply_link'    => esc_html__( 'Cancel Reply',             'al-qamara-lite' ),
        'label_submit'         => esc_html__( 'Post Comment',             'al-qamara-lite' ),
        'submit_button'        => '<button type="submit" name="%1$s" id="%2$s" class="%3$s btn btn-custom px-5 py-2">%4$s <i class="fas fa-paper-plane ms-2" aria-hidden="true"></i></button>',
        'submit_field'         => '<div class="form-submit mt-3">%1$s %2$s</div>',
        'comment_notes_before' => '<p class="comment-notes text-muted">' . esc_html__( 'Your email address will not be published. Required fields are marked *', 'al-qamara-lite' ) . '</p>',
        'comment_notes_after'  => '',
        'logged_in_as'         => '',
        'fields' => [

            'author' => '<div class="row g-3 mb-3"><div class="col-md-6"><label for="author" class="form-label">'
                . esc_html__( 'Name', 'al-qamara-lite' )
                . ( $alqamara_req ? ' <span class="text-danger">*</span>' : '' ) . '</label>'
                . '<input type="text" id="author" name="author" class="form-control"' . ( $alqamara_req ? ' required' : '' ) . ' placeholder="'
                . esc_attr__( 'Your name', 'al-qamara-lite' )
                . '" value="' . esc_attr( $commenter['comment_author'] ?? '' ) . '"></div>',

            'email'  => '<div class="col-md-6"><label for="email" class="form-label">'
                . esc_html__( 'Email', 'al-qamara-lite' )
                . ( $alqamara_req ? ' <span class="text-danger">*</span>' : '' ) . '</label>'
                . '<input type="email" id="email" name="email" class="form-control"' . ( $alqamara_req ? ' required' : '' ) . ' placeholder="'
                . esc_attr__( 'your@email.com', 'al-qamara-lite' )
                . '" value="' . esc_attr( $commenter['comment_author_email'] ?? '' ) . '"></div></div>',

            'url'    => '<div class="mb-3"><label for="url" class="form-label">'
                . esc_html__( 'Website', 'al-qamara-lite' )
                . '</label>'
                . '<input type="url" id="url" name="url" class="form-control" placeholder="'
                . esc_attr__( 'https://yourwebsite.com (optional)', 'al-qamara-lite' )
                . '" value="' . esc_attr( $commenter['comment_author_url'] ?? '' ) . '"></div>',

            'cookies' => '<div class="mb-3 form-check">'
                . '<input type="checkbox" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" class="form-check-input" value="yes"' . ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' ) . '>'
                . '<label for="wp-comment-cookies-consent" class="form-check-label small text-muted">'
                . esc_html__( 'Save my name, email, and website for the next time I comment.', 'al-qamara-lite' )
                . '</label></div>',
        ],

        'comment_field' => '<div class="mb-3"><label for="comment" class="form-label">'
            . esc_html__( 'Comment', 'al-qamara-lite' )
            . ' <span class="text-danger">*</span></label>'
            . '<textarea id="comment" name="comment" class="form-control" rows="6" required placeholder="'
            . esc_attr__( 'Write your comment here...', 'al-qamara-lite' )
            . '"></textarea></div>',

    ] );
    ?>
    <!-- ===================== COMMENT FORM END ===================== -->


</div>
<!-- ===================== COMMENTS SECTION END ===================== -->
