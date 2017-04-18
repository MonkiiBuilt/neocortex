VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |node_config|

  node_config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  node_config.vm.box = "boxcutter/ubuntu1604"
#  node_config.vm.box_url = "http://my.monkii.com.au/vagrant/boxes/ubuntu-xenial64-monkii-vagrant.box"

  node_config.vm.provision :shell, path: "setup/bootstrap.sh"
  node_config.vm.provision :shell, path: "setup/startup.sh", run: "always"

  node_config.vm.network :private_network, ip: "192.168.56.102"
  node_config.vm.network :forwarded_port, guest: 80, host: 9999

  node_config.vm.provider :virtualbox do |vb|
    vb.customize [
      "modifyvm", :id,
      "--name", "neocortex",
      "--natdnshostresolver1", "on",
      "--memory", "1024"
    ]
  end

  # Share project folder (where Vagrantfile is located) as /vagrant
  if RUBY_PLATFORM.include? "linux"
    node_config.vm.synced_folder ".", "/vagrant",
        nfs: true,
        nfs_udp: false,
        :mount_options => ['nolock']
  else
    node_config.vm.synced_folder ".", "/vagrant", nfs: true
  end

end
