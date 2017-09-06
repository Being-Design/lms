if ( typeof jq == "undefined" ) {
    var jq = jQuery;
}

jq(document).on( 'ready', function($) {

    /**
     * Learndash group edit screen JS
     */
    var BuddyPress_Learndash_Group_Edit = {

        vars: {
            $enrollmentNotice: jq('#enrollment-notice'),
            startPos: 0,
            endPos: 10,
        },

        init: function() {
            if ( typeof buddypress_learndash_vars != 'undefined' ) {
                BuddyPress_Learndash_Group_Edit.vars.$enrollmentNotice.toggleClass('hidden');
                this.user_enrollment(buddypress_learndash_vars.users.slice(BuddyPress_Learndash_Group_Edit.vars.startPos, BuddyPress_Learndash_Group_Edit.vars.endPos));
            }
        },

        user_enrollment: function( users ) {

            if ( 0 == users.length ) {
                BuddyPress_Learndash_Group_Edit.vars.$enrollmentNotice.toggleClass('hidden');
                return 0;
            }

            jq.ajax({
                type: 'POST',
                url: ajaxurl,
                data: { action: "mass_group_join", users: users, courses: buddypress_learndash_vars.courses },
                success: function( response ) {
                    if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
                        response = new Object;
                        response.success = false;
                        return;
                    }

                    BuddyPress_Learndash_Group_Edit.vars.startPos = BuddyPress_Learndash_Group_Edit.vars.endPos;
                    BuddyPress_Learndash_Group_Edit.vars.endPos = BuddyPress_Learndash_Group_Edit.vars.endPos + 10;
                    BuddyPress_Learndash_Group_Edit.user_enrollment( buddypress_learndash_vars.users.slice( BuddyPress_Learndash_Group_Edit.vars.startPos, BuddyPress_Learndash_Group_Edit.vars.endPos ) );
                }
            });
        }

    };

    BuddyPress_Learndash_Group_Edit.init();

});