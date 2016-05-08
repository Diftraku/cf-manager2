#!/bin/bash
################################################################################
# tmuxgo - Dale Bewley <dale @ bewley net> - Sat Feb 19 08:53:30 PST 2011
#-------------------------------------------------------------------------------
# Use me to get your tmux session restored after a reboot or reattach daily.
# Just type tmuxgo every morning and hit ^bd at the end of the day. Login
# remotely and do the same.
#
# Attaches to an existing session named $SESSION or will create one if missing.
# The created session will be pre-populated with a number of windows. 
#
# For example, window 0 running IRC, window 1 running email, window 2 logged
# into a router used daily.
#
#
# Bugs & Todos:
#       o If session already exists, instantiate any missing windows.
#         This could be done by checking tmux list-windows, not sure needed.
#
#       o Window 0 automatically changes name to 'weechat 0.3.3', ignoring 
#         the -n option. The following should fix it, but does not:
#               tmux set-window-option -t $SESSION:0 automatic-rename off
#         Same thing happens when issuing configure command on Arista switches.
#         Workaround for that is 'env TERM=vt100 ssh switch'.
#         Note that name (#W) and title (#T) are not necessarily the same value.
#
################################################################################

# the name of your primary tmux session
SESSION="cfm-dev"

byobu-tmux has-session -t $SESSION
if [ $? -eq 0 ]; then
        echo "Session $SESSION already exists."
        sleep 1
        exit 0;
else
        # create a new session, named $SESSION, and detach from it
        echo "Creating $SESSION session"
        byobu new -d -s $SESSION
fi

# set specific bits
byobu-tmux set -t $SESSION -g base-index 1 
byobu-tmux set -t $SESSION:1 -g automatic-rename off 

# create our windows
byobu-tmux new-window    -t $SESSION -a -n 'logs'    'tail -f /var/log/nginx/cfm.error.log -f ~/projects/cf-manager2/logs/app.log'

# you may need to cycle through windows and type in passwords
# if you don't use ssh keys
byobu-tmux rename-window -t $SESSION:1 "shell"
byobu-tmux -2 attach -t $SESSION
