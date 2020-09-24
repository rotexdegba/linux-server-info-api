[Documentation Home](../index.md) > [/server/server-overview](../server-server-overview.md)

# Server Overview Info

## Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| host_name | The host name of the machine this web-application is running on. Could be empty if it cannot be retrieved.| String |
| os_family | The operating system (OS) family name of the OS running on the machine this web-application is running on. It is always one of the following: **Windows**, **BSD**, **Darwin**, **Solaris**, **Linux** or **Unknown**.| String |
| kernel_version | The kernel version of the operating system (OS) running on the machine this web-application is running on. Could be empty if it cannot be retrieved.| String |
| distro_name | The precise name and usually major version number of the operating system (OS) running on the machine this web-application is running on. Could be empty if it cannot be retrieved. | String |
| architecture | The architecture (e.g. **i386**, **AMD64**, etc.) of the machine this web-application is running on. Could be empty if it cannot be retrieved. | String |
| system_model | The manufacturer assigned model name of the machine this web-application is running on. Could be empty if it cannot be retrieved. | String |
| uptime | Total number of seconds the system has been up (**-1** means info could not be retrieved). | Integer |
| uptime_text | Human readable form (in English) of the total number of seconds the system has been up. Could be empty if it cannot be retrieved. | String |
| last_booted_timestamp | Unix Timestamp value representing the exact time the system was last booted (**-1** means info could not be retrieved). | Integer |
| web_software | Description of the web server software powering this web-application on the machine it is running on. **Unknown** is returned if the information could not be retrieved. | String |
| php_version | The version of PHP powering this web-application on the machine it is running on. | String |
| virtualization | Virtualization information about the machine this web-application is running on if and only if it is a virtual machine instance. Could be empty if it cannot be retrieved or if the machine this web-application is running on is not a virtual machine. | String |
| free_ram_bytes | The amount of free ram in bytes (**-1** means info could not be retrieved). | Integer |
| free_swap_bytes | The amount of free swap memory in bytes (**-1** means info could not be retrieved) | 
| used_ram_bytes | The amount of used ram in bytes (**-1** means info could not be retrieved). | Integer |
| used_swap_bytes | The amount of used swap memory in bytes (**-1** means info could not be retrieved). | Integer |
| total_ram_bytes | The amount of total ram in bytes (**-1** means info could not be retrieved). | Integer |
| total_swap_bytes | The amount of total swap memory in bytes (**-1** means info could not be retrieved). | Integer |
| overall_cpu_usage_percent | The overall CPU usage percentage value (**-1.0** means info could not be retrieved). | Float |
| total_num_physical_cpu_cores | Total number of physical CPU cores on the machine this web-application is being run on (**-1** means info could not be retrieved). | Integer |
| total_num_virtual_or_logical_processors | Total number of virtual or logical processors on the machine this web-application is being run on (**-1** means info could not be retrieved). | Integer |
| total_number_of_processes | Total number of processes on the machine this web-application is being run on (**-1** means info could not be retrieved). | Integer |
| total_number_of_threads | Total number of threads on the machine this web-application is being run on (**-1** means info could not be retrieved). | Integer |
| total_number_of_running_processes_linux | Total number of running processes on the machine this web-application is being run on (**-1** means info could not be retrieved). Applies to Linux-like Operating Systems only. | Integer |
| total_number_of_sleeping_processes_linux | Total number of sleeping processes on the machine this web-application is being run on (**-1** means info could not be retrieved). Applies to Linux-like Operating Systems only. | Integer |
| total_number_of_stopped_processes_linux | Total number of stopped processes on the machine this web-application is being run on (**-1** means info could not be retrieved). Applies to Linux-like Operating Systems only. | Integer |
| total_number_of_zombie_processes_linux | Total number of zombie processes on the machine this web-application is being run on (**-1** means info could not be retrieved). Applies to Linux-like Operating Systems only. | Integer |
| number_of_logged_in_users | Total number of users logged in to the machine this web-application is being run on (**-1** means info could not be retrieved). | Integer |
| cpus_info | An array of [CPU Info](cpu-info.md) objects, each representing information about each cpu on the system this application is running on | Array |
| selinux_enabled | Indicates whether SELinux functionality is enabled if the Operating System this web-application is being run on has SELinux capabilities. A value of **1** means that SELinux is enabled.  A value of **0** means that SELinux is disabled.  A value of **-1** means that SELinux is not supported. | Integer |
| selinux_mode | A string indicating what mode SELinux is operating in on the machine this web-application on. Will be empty if SELinux is not supported. | String |
| selinux_policy | The name of the currently loaded SELinux policy. Will be empty if SELinux is not supported. | String |
