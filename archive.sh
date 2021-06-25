LOCK='archive.lock'

start()
{
if [ "$(is_running)" != "1" ]; then
	printf "Starting archive service...\n";
	nohup php artisan facebook:index service &> archive.log&
	echo $! > $LOCK
else
	printf "Already running...\n";
fi
	status
}
stop()
{
	if [ -f $LOCK ]; then
		printf "Stopping... \n";
		kill -9 $(cat $LOCK)
		rm -f $LOCK
		printf "Stopped!\n";
	fi
}
status()
{
	PID=$(cat $LOCK)
	running=$(is_running)
	if [ "$running" = "1" ]; then
		printf "\033[0;32mRunning: $PID\033[0m\n"
	elif [ "$running" = "-1" ]; then
		printf "\033[0;33mProcess exists in lock file but not running\033[0m\n"
	else
		printf "\033[0;33mNot running\033[0m\n"
	fi
}
is_running(){
	local  result
	if [ -f $LOCK ]; then
		local PID=$(cat $LOCK)
		if ps -p $PID > /dev/null
		then
			result=1;
		else
			result=-1
		fi
	else
		result=0
	fi
	echo $result;
}
CMD=$1;
if [ "$1" = "stop" ]; then
	stop
elif [ "$1" = "start" ]; then
	start
elif [ "$1" = "restart" ]; then
	stop
	start
else
	status
fi
