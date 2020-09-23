[Documentation Home](index.md)

# Network Info

## Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| name | The name of the network interface device represented by this object. | String |
| speed_bits_per_second | The speed in bits per second of the network interface device represented by this object. A value of **-1** or **-1.0** indicates that the value could not be retrieved. | Float |
| type | A string representing the type of network interface device this object represents. A value of **unknown** is returned if it cannot be determined. | String |
| state | A string representing the current state of the network interface device this object represents. A value of **unknown** is returned if it cannot be determined. | String |
| num_bytes_received | The number of bytes received on the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| num_received_errors | The number of errors received on the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| num_received_packets | The number of packets received on the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| num_bytes_sent | The number of bytes sent from the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| num_sent_errors | The number of errors sent from the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| num_sent_packets | The number of packets sent from the network interface device represented by this object. A value of **-1**  indicates that the value could not be retrieved. | Integer |
| gateway | The IP address of another device (on the same local network with the network interface device represented by this object) that is responsible for transmitting and receiving packets to and from other networks outside the local network. It will be empty if the address cannot be determined. | String |
| ipv4 | The IP version 4 address of the network interface device represented by this object. It will be empty if the address cannot be determined. | String |
| mac | The MAC address of the network interface device represented by this object. It will be empty if the address cannot be determined. | String |