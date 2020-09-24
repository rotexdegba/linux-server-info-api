[Documentation Home](../index.md) > [/server/disk-drives-info](../server-disk-drives-info.md)

# Disk Drive Info

## Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| name | The name of the drive represented by this object. Could be empty if the name could not be retrieved.| String |
| vendor | The name of the manufacturer of the drive represented by this object. Could be empty if the name could not be retrieved. | String |
| device | A unique string identifier representing the drive as a unique device in the system. Could be empty if value can't be retrieved | String |
| bytes_read | Total non-negative number of bytes read from the device. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| bytes_written | Total non-negative number of bytes written to the device. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| size_in_bytes | Total non-negative number of storage capacity in bytes of the device. A value of **-1** or **-1.0** means that the value could not be retrieved. | Float |
| partitions | An array of [Disk Drive Partition Info](disk-drive-partition-info.md) objects each representing a partition of a disk drive | Array |
