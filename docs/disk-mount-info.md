[Documentation Home](index.md)

# Disk Mount Info

## Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| name | The name of the disk mount represented by this object. Could be empty if the name could not be retrieved.| String |
| mount_point | A unique system generated string identifier for the disk mount represented by this object. Could be empty if the value could not be retrieved. | String |
| type | A string representing the type of file-system used by the disk mount. Could be empty if the value could not be retrieved. | String |
| size_in_bytes | Total non-negative number of storage capacity in bytes of the disk mount. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| used_bytes | Total non-negative number of used storage capacity in bytes of the disk mount. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| free_bytes | Total non-negative number of free storage capacity in bytes of the disk mount. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| free_percent | Total non-negative number representing the percentage of free storage capacity of the disk mount. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| used_percent | Total non-negative number representing the percentage of used storage capacity of the disk mount. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| options | An array of Strings, each of which represents a mount option for the disk mount | Array |
