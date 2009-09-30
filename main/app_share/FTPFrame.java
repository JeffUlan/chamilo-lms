// Copyright (C) 2002-2005 Ultr@VNC Team.  All Rights Reserved.
// Copyright (C) 2004 Kenn Min Chong, John Witchel.  All Rights Reserved.
//
//This is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.
//
//This software is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this software; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,
//USA.
//


import javax.swing.JFrame;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.util.ArrayList;
import java.util.Vector;
import javax.swing.*;


/*
 * Created on Feb 25, 2004
 *
 */

/**
 * @author John Witchel, Kenn Min Chong
 *
 */
public class FTPFrame extends JFrame implements ActionListener, MouseListener {
	VncViewer viewer;

	private javax.swing.JPanel jContentPane = null;
	private javax.swing.JPanel topPanel = null;
	private javax.swing.JPanel topPanelLocal = null;
	private javax.swing.JPanel topPanelRemote = null;
	private javax.swing.JPanel topPanelCenter = null;
	private javax.swing.JPanel statusPanel = null;
	private javax.swing.JPanel remotePanel = null;
	private javax.swing.JPanel localPanel = null;
	private javax.swing.JPanel buttonPanel = null;
	private javax.swing.JButton sendButton = null;
	private javax.swing.JButton receiveButton = null;
	private javax.swing.JButton deleteButton = null;
	private javax.swing.JButton newFolderButton = null;
	private javax.swing.JButton stopButton = null;
	private javax.swing.JButton closeButton = null;
	private javax.swing.JButton dummyButton = null;
	private javax.swing.JComboBox localDrivesComboBox = null;
	private javax.swing.JComboBox remoteDrivesComboBox = null;
	private javax.swing.JTextField localMachineLabel = null;
	private javax.swing.JTextField remoteMachineLabel = null;
	private javax.swing.JButton localTopButton = null;
	private javax.swing.JButton remoteTopButton = null;
	private javax.swing.JScrollPane localScrollPane = null;
	private javax.swing.JList localFileTable = null;
	private javax.swing.JScrollPane remoteScrollPane = null;
	private javax.swing.JList remoteFileTable = null;
	private javax.swing.JTextField remoteLocation = null;
	private javax.swing.JTextField localLocation = null;
	private javax.swing.JTextField localStatus = null;
	public javax.swing.JTextField remoteStatus = null;
	public javax.swing.JComboBox historyComboBox = null;
	public javax.swing.JProgressBar jProgressBar = null;
	public javax.swing.JTextField connectionStatus = null;
	public boolean updateDriveList;
	private Vector remoteList = null;
	private Vector localList = null;
	private File currentLocalDirectory = null;	// Holds the current local Directory
	private File currentRemoteDirectory = null;	// Holds the current remote Directory
	private File localSelection = null;		// Holds the currently selected local file  
	private String remoteSelection = null;	// Holds the currently selected remote file
	public String selectedTable = null;
	
//	 sf@2004 - Separate directories and files for better lisibility
	private ArrayList DirsList;
	private ArrayList FilesList;	

	public static void main(String[] args) {
	}
	/**
	 * This is the default constructor
	 
	public FTPFrame() {
		super();
		initialize();
	}
	*/

	/**
	 * This is Kenn's Constructor
	 *
	 */
	FTPFrame(VncViewer v) {
		super("Ultr@VNC File Transfer");
		viewer = v;
		// this.setUndecorated(true); // sf@2004
		this.setResizable(false);  // sf@2004
		setSize(320, 240);
		
		// sf@2004
		DirsList = new ArrayList();
		FilesList = new ArrayList();
		
		initialize();
	}
	
	 /* Refreshing local and remote directory lists
	  * after an operation has been performed
	 */
	 void refreshLocalLocation()
	 {
	 	File f = new File(localLocation.getText());
	 	this.changeLocalDirectory(f);
	 }
	 
	 void refreshRemoteLocation()
	 {
		remoteList.clear();
		remoteFileTable.setListData(remoteList);	
		viewer.rfb.readServerDirectory(remoteLocation.getText());
	 }
	 
	/*
	 * Prints the list of drives on the remote directory and returns a String[].  
	 * str takes as string like A:fC:lD:lE:lF:lG:cH:c
	 * in the form Drive Letter:Drive Type where 
	 * f = floppy, l = local drive, c=CD-ROM, n = network
	 */
	String[] printDrives(String str) {
		System.out.println(str);
		updateDriveList = true;
		remoteDrivesComboBox.removeAllItems();
		int size = str.length();
		String driveType = null;
		String[] drive = new String[str.length() / 3];

		// Loop through the string to create a String[]
		for (int i = 0; i < size; i = i + 3) {
			drive[i / 3] = str.substring(i, i + 2);
			driveType = str.substring(i + 2, i + 3);
			if (driveType.compareTo("f") == 0)
				drive[i / 3] += "\\ Floppy";
			if (driveType.compareTo("l") == 0)
				drive[i / 3] += "\\ Local Disk";
			if (driveType.compareTo("c") == 0)
				drive[i / 3] += "\\ CD-ROM";
			if (driveType.compareTo("n") == 0)
				drive[i / 3] += "\\ Network";

			remoteDrivesComboBox.addItem(drive[i / 3]);
		}
		//sf@ - Select Drive C:as default if possible
		boolean bFound = false;
		for(int i = 0; i < remoteDrivesComboBox.getItemCount() ; i++)
		{
			if(remoteDrivesComboBox.getItemAt(i).toString().substring(0,1).toUpperCase().equals("C"))
			{
				remoteDrivesComboBox.setSelectedIndex(i);
				bFound = true;
			}
		}
		if (!bFound) remoteDrivesComboBox.setSelectedIndex(0);
		updateDriveList = false;
		return drive;
	}
	
	/*Disable buttons/lists while file transfer is in progress*/
	
	public void disableButtons()
	{
		closeButton.setEnabled(false);
		deleteButton.setEnabled(false);
		localTopButton.setEnabled(false);
		newFolderButton.setEnabled(false);
		stopButton.setVisible(true);
		stopButton.setEnabled(true);
		receiveButton.setEnabled(false);
		remoteTopButton.setEnabled(false);
		sendButton.setEnabled(false);
		remoteFileTable.setEnabled(false);
		localFileTable.setEnabled(false);	
		localLocation.setEnabled(false);
		remoteLocation.setEnabled(false);	
		remoteDrivesComboBox.setEnabled(false);
		localDrivesComboBox.setEnabled(false);
		setDefaultCloseOperation(JFrame.DO_NOTHING_ON_CLOSE); // sf@2004
		
	}
	/*Enable buttons/lists after file transfer is done*/
	
	public void enableButtons()
	{
		closeButton.setEnabled(true);
		deleteButton.setEnabled(true);
		localTopButton.setEnabled(true);
		newFolderButton.setEnabled(true);
		stopButton.setVisible(false);
		stopButton.setEnabled(false);
		receiveButton.setEnabled(true);
		remoteTopButton.setEnabled(true);
		sendButton.setEnabled(true);
		remoteFileTable.setEnabled(true);
		localFileTable.setEnabled(true);
		localLocation.setEnabled(true);		
		remoteLocation.setEnabled(true);
		remoteDrivesComboBox.setEnabled(true);
		localDrivesComboBox.setEnabled(true);
		// setDefaultCloseOperation(JFrame.HIDE_ON_CLOSE); // sf@2004
	}

	/*
	 * Print Directory prints out all the contents of a directory
	 */
	void printDirectory(ArrayList a) {

		for (int i = 0; i < a.size(); i++) {
			remoteList.addElement(a.get(i));
		}
		remoteFileTable.setListData(remoteList);
	}

	/**
	 * This method initializes this
	 * 
	 * @return void
	 */
	private void initialize() {
		this.setSize(794, 500);
		this.setContentPane(getJContentPane());
		updateDriveList = true;
		}
	/**
	 * This method initializes jContentPane.  This is the main content pane
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getJContentPane() {
		if (jContentPane == null) {
			jContentPane = new javax.swing.JPanel();
			jContentPane.setLayout(new java.awt.BorderLayout());
			jContentPane.add(getTopPanel(), java.awt.BorderLayout.NORTH);
			jContentPane.add(getStatusPanel(), java.awt.BorderLayout.SOUTH);
			jContentPane.add(getRemotePanel(), java.awt.BorderLayout.EAST);
			jContentPane.add(getLocalPanel(), java.awt.BorderLayout.WEST);
			jContentPane.add(getButtonPanel(), java.awt.BorderLayout.CENTER);
		}
		return jContentPane;
	}
	/**
	 * This method initializes topPanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getTopPanelLocal() {
		if (topPanelLocal == null) {
			topPanelLocal = new javax.swing.JPanel();
			topPanelLocal.setLayout(new java.awt.BorderLayout());
			topPanelLocal.setPreferredSize(new java.awt.Dimension(325, 22));
			topPanelLocal.add(getLocalDrivesComboBox(), java.awt.BorderLayout.WEST);
			topPanelLocal.add(getLocalMachineLabel(), java.awt.BorderLayout.CENTER);
			topPanelLocal.add(getLocalTopButton(), java.awt.BorderLayout.EAST);
			topPanelLocal.setBackground(java.awt.Color.lightGray);
		}
		return topPanelLocal;
	}
	
	/**
	 * This method initializes topPanelRemote
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getTopPanelRemote() {
		if (topPanelRemote == null) {
			topPanelRemote = new javax.swing.JPanel();
			topPanelRemote.setLayout(new java.awt.BorderLayout());
			topPanelRemote.setPreferredSize(new java.awt.Dimension(325, 20));
			topPanelRemote.add(getRemoteDrivesComboBox(), java.awt.BorderLayout.WEST);
			topPanelRemote.add(getRemoteMachineLabel(), java.awt.BorderLayout.CENTER);
			topPanelRemote.add(getRemoteTopButton(), java.awt.BorderLayout.EAST);
			topPanelRemote.setBackground(java.awt.Color.lightGray);
		}
		return topPanelRemote;
	}

	/**
	 * This method initializes topPanelRemote
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getTopPanelCenter() {
		if (topPanelCenter == null) {
			topPanelCenter = new javax.swing.JPanel();
			topPanelCenter.add(getDummyButton(), null);
		}
		return topPanelCenter;
	}
	
	/**
	 * This method initializes topPanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getTopPanel() {
		if (topPanel == null) {
			topPanel = new javax.swing.JPanel();
			topPanel.setLayout(new java.awt.BorderLayout());
			//sf@2004 - We manage 2 top panels
			topPanel.add(getTopPanelLocal(), java.awt.BorderLayout.WEST);
			// topPanel.add(getTopPanelCenter(), java.awt.BorderLayout.CENTER);
			topPanel.add(getTopPanelRemote(), java.awt.BorderLayout.EAST);
						
			/*
			topPanel.add(getLocalDrivesComboBox(), null);
			topPanel.add(getLocalMachineLabel(), null);
			topPanel.add(getLocalTopButton(), null);
			topPanel.add(getRemoteDrivesComboBox(), null);
			topPanel.add(getRemoteMachineLabel(), null);
			topPanel.add(getRemoteTopButton(), null);
			topPanel.setBackground(java.awt.Color.lightGray);
			*/
		}
		return topPanel;
	}

	/**
	 * This method initializes statusPanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getStatusPanel() {
		if (statusPanel == null) {
			statusPanel = new javax.swing.JPanel();
			statusPanel.setLayout(
				new javax.swing.BoxLayout(
					statusPanel,
					javax.swing.BoxLayout.Y_AXIS));
			statusPanel.add(getHistoryComboBox(), null);
			statusPanel.add(getJProgressBar(), null);
			statusPanel.add(getConnectionStatus(), null);
			statusPanel.setBackground(java.awt.Color.lightGray);
			
		}
		return statusPanel;
	}
	/**
	 * This method initializes remotePanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getRemotePanel() {
		if (remotePanel == null) {
			remotePanel = new javax.swing.JPanel();
			remotePanel.setLayout(
				new javax.swing.BoxLayout(
					remotePanel,
					javax.swing.BoxLayout.Y_AXIS));
			remotePanel.add(getRemoteLocation(), null);
			remotePanel.add(getRemoteScrollPane(), null);
			remotePanel.add(getRemoteStatus(), null);
			remotePanel.setBackground(java.awt.Color.lightGray);
		}
		return remotePanel;
	}
	/**
	 * This method initializes localPanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getLocalPanel() {
		if (localPanel == null) {
			localPanel = new javax.swing.JPanel();
			localPanel.setLayout(
				new javax.swing.BoxLayout(
					localPanel,
					javax.swing.BoxLayout.Y_AXIS));
			localPanel.add(getLocalLocation(), null);
			localPanel.add(getLocalScrollPane(), null);
			localPanel.add(getLocalStatus(), null);
			localPanel.setBackground(java.awt.Color.lightGray);
			localPanel.setComponentOrientation(
				java.awt.ComponentOrientation.UNKNOWN);
			localPanel.setName("localPanel");
		}
		return localPanel;
	}
	/**
	 * This method initializes buttonPanel
	 * 
	 * @return javax.swing.JPanel
	 */
	private javax.swing.JPanel getButtonPanel()
	{
		if (buttonPanel == null)
		{
			buttonPanel = new javax.swing.JPanel();
			buttonPanel.setLayout(null);
			buttonPanel.add(getReceiveButton(), null);
			buttonPanel.add(getNewFolderButton(), null);
			buttonPanel.add(getCloseButton(), null);
			buttonPanel.add(getDeleteButton(), null);
			buttonPanel.add(getSendButton(), null);
			buttonPanel.add(getStopButton(), null);
			buttonPanel.setBackground(java.awt.Color.lightGray);
		}
		return buttonPanel;
	}
	/**
	 * This method initializes sendButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getSendButton() {
		if (sendButton == null) {
			sendButton = new javax.swing.JButton();
			sendButton.setBounds(20, 30, 97, 25);
			sendButton.setText("Send >>");
			sendButton.setName("sendButton");
			sendButton.addActionListener(this);

		}
		return sendButton;
	}
	/**
	 * This method initializes receiveButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getReceiveButton() {
		if (receiveButton == null) {
			receiveButton = new javax.swing.JButton();
			receiveButton.setBounds(20, 60, 97, 25);
			receiveButton.setText("<< Receive");
			receiveButton.setName("receiveButton");
			receiveButton.addActionListener(this);
		}
		return receiveButton;
	}
	/**
	 * This method initializes deleteButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getDeleteButton() {
		if (deleteButton == null) {
			deleteButton = new javax.swing.JButton();
			deleteButton.setBounds(20, 110, 97, 25);
			deleteButton.setText("Delete File");
			deleteButton.setName("deleteButton");
			deleteButton.addActionListener(this);
		}
		return deleteButton;
	}
	/**
	 * This method initializes newFolderButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getNewFolderButton() {
		if (newFolderButton == null) {
			newFolderButton = new javax.swing.JButton();
			newFolderButton.setBounds(20, 140, 97, 25);
			newFolderButton.setText("New Folder");
			newFolderButton.setName("newFolderButton");
			newFolderButton.addActionListener(this);
		}
		return newFolderButton;
	}
	
	/**
	 * This method initializes stopButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getStopButton()
	{
		if (stopButton == null)
		{
			stopButton = new javax.swing.JButton();
			stopButton.setBounds(20, 200, 97, 25);
			stopButton.setText("Stop");
			stopButton.setName("stopButton");
			stopButton.addActionListener(this);
			stopButton.setVisible(false);
		}
		return stopButton;
	}
	
	/**
	 * This method initializes closeButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getCloseButton() {
		if (closeButton == null) {
			closeButton = new javax.swing.JButton();
			closeButton.setBounds(20, 325, 97, 25);
			closeButton.setText("Close");
			closeButton.setName("closeButton");
			closeButton.addActionListener(this);
		}
		return closeButton;
	}
	
	/**
	 * This method initializes dummyButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getDummyButton() {
		if (dummyButton == null) {
			dummyButton = new javax.swing.JButton();
			dummyButton.setBounds(12, 206, 99, 25);
			dummyButton.setText("aaaaaaaaaaaaaaa");
			dummyButton.setName("DummyButton");
			dummyButton.setVisible(false);
		}
		return dummyButton;
	}
	
	/**
	 * This method initializes localDrivesComboBox
	 * 
	 * @return javax.swing.JComboBox
	 */
	private javax.swing.JComboBox getLocalDrivesComboBox() {
		updateDriveList = true;
		// Read in Drive letters from local disk
		File[] roots = File.listRoots();
		String[] localDisks = new String[roots.length];
		for (int i = 0; i < roots.length; i++) {
			localDisks[i] = roots[i].toString();
		}

		// Create the combo box
		if (localDrivesComboBox == null) {
			localDrivesComboBox = new javax.swing.JComboBox(localDisks);
			localDrivesComboBox.setName("LocalDisks");
			localDrivesComboBox.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));

			//Select the second entry (e.g. C:\)
			// localDrivesComboBox.setSelectedIndex(1);
			localDrivesComboBox.addActionListener(this);
		}
		updateDriveList = false;
		return localDrivesComboBox;
	}
	/**
	 * This method initializes remoteDrivesComboBox
	 * 
	 * @return javax.swing.JComboBox
	 */
	public javax.swing.JComboBox getRemoteDrivesComboBox() {
		if (remoteDrivesComboBox == null) {
			remoteDrivesComboBox = new javax.swing.JComboBox();
			remoteDrivesComboBox.setName("remoteDisks");
			remoteDrivesComboBox.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
			remoteDrivesComboBox.addActionListener(this);

		}
		return remoteDrivesComboBox;
	}
	/**
	 * This method initializes localMachineLabel
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getLocalMachineLabel() {
		if (localMachineLabel == null) {
			localMachineLabel = new javax.swing.JTextField();
			localMachineLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
			// localMachineLabel.setPreferredSize(new java.awt.Dimension(150, 19));
			localMachineLabel.setBackground(java.awt.Color.lightGray);
			localMachineLabel.setText("             LOCAL MACHINE");
			localMachineLabel.setName("localLocation");
			localMachineLabel.setFont(
				new java.awt.Font("Dialog", java.awt.Font.BOLD, 11));
			localMachineLabel.setEditable(false);
		}
		return localMachineLabel;
	}
	/**
	 * This method initializes remoteMachineLabel
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getRemoteMachineLabel() {
		if (remoteMachineLabel == null) {
			remoteMachineLabel = new javax.swing.JTextField();
			// remoteMachineLabel.setPreferredSize(new java.awt.Dimension(150, 19));
			remoteMachineLabel.setName("remoteLocation");
			remoteMachineLabel.setText("        REMOTE MACHINE");
			remoteMachineLabel.setBackground(java.awt.Color.lightGray);
			remoteMachineLabel.setFont(
				new java.awt.Font("Dialog", java.awt.Font.BOLD, 11));
			remoteMachineLabel.setEditable(false);
				
		}
		return remoteMachineLabel;
	}
	/**
	 * This method initializes localTopButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getLocalTopButton() {
		if (localTopButton == null) {
			localTopButton = new javax.swing.JButton();
			localTopButton.setText("Root (\\)");
			// localTopButton.setPreferredSize(new java.awt.Dimension(30, 19));
			localTopButton.setFont(
				new java.awt.Font("Dialog", java.awt.Font.BOLD, 10));
			localTopButton.addActionListener(this);
		}
		return localTopButton;
	}
	/**
	 * This method initializes remoteTopButton
	 * 
	 * @return javax.swing.JButton
	 */
	private javax.swing.JButton getRemoteTopButton() {
		if (remoteTopButton == null) {
			remoteTopButton = new javax.swing.JButton();
			remoteTopButton.setText("Root (\\)");
			// remoteTopButton.setPreferredSize(new java.awt.Dimension(49, 25));
			remoteTopButton.setFont(
				new java.awt.Font("Dialog", java.awt.Font.BOLD, 10));
			remoteTopButton.addActionListener(this);
		}
		return remoteTopButton;
	}
	/**
	 * This method initializes localFileTable
	 * 
	 * @return javax.swing.JTable
	 */

	private javax.swing.JList getLocalFileTable() {
		if (localFileTable == null) {
			localList = new Vector(0);
			localFileTable = new JList(localList);
			localFileTable.addMouseListener(this);
			localFileTable.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
		}
		return localFileTable;
	}
	/**
	 * This method initializes localScrollPane
	 * 
	 * @return javax.swing.JScrollPane
	 */
	private javax.swing.JScrollPane getLocalScrollPane() {
		if (localScrollPane == null) {
			localScrollPane = new javax.swing.JScrollPane();
			localScrollPane.setViewportView(getLocalFileTable());
			localScrollPane.setPreferredSize(new java.awt.Dimension(325, 418));
			localScrollPane.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
			localScrollPane.setName("localFileList");
		}
		return localScrollPane;
	}
	/**
	 * This method initializes remoteFileTable
	 * 
	 * @return javax.swing.JTable
	 */
	private javax.swing.JList getRemoteFileTable() {
		if (remoteFileTable == null) {
			remoteList = new Vector(0);
			remoteFileTable = new JList(remoteList);
			remoteFileTable.addMouseListener(this);
			remoteFileTable.setSelectedValue("C:\\", false);
			remoteFileTable.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
			
		}
		return remoteFileTable;
	}
	/**
	 * This method initializes remoteScrollPane
	 * 
	 * @return javax.swing.JScrollPane
	 */
	private javax.swing.JScrollPane getRemoteScrollPane() {
		if (remoteScrollPane == null) {
			remoteScrollPane = new javax.swing.JScrollPane();
			remoteScrollPane.setViewportView(getRemoteFileTable());
			remoteScrollPane.setPreferredSize(new java.awt.Dimension(325, 418));
		}
		return remoteScrollPane;
	}
	/**
	 * This method initializes remoteLocation
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getRemoteLocation()
	{
		if (remoteLocation == null)
		{
			remoteLocation = new javax.swing.JTextField();
			remoteLocation.setText("");
			remoteLocation.setEditable(false); // sf@2004
			remoteLocation.setBackground(new Color(255,255,238));
			remoteLocation.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
		}
		return remoteLocation;
	}
	/**
	 * This method initializes localLocation
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getLocalLocation() {
		if (localLocation == null) {
			localLocation = new javax.swing.JTextField();
			localLocation.setText("");
			localLocation.setEditable(false); // sf@2004
			localLocation.setBackground( new Color(255,255,238));
			localLocation.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
		}
		return localLocation;
	}
	/**
	 * This method initializes localStatus
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getLocalStatus() {
		if (localStatus == null) {
			localStatus = new javax.swing.JTextField();
			//		localStatus.setText("> Found 63 File(s) 7 Directorie(s)");
			localStatus.setBackground(java.awt.Color.lightGray);
			localStatus.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
			localStatus.setEditable(false);
		}
		return localStatus;
	}
	/**
	 * This method initializes remoteStatus
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getRemoteStatus() {
		if (remoteStatus == null) {
			remoteStatus = new javax.swing.JTextField();
			//		remoteStatus.setText("> Found 15 File(s) 2 Directorie(s)");
			remoteStatus.setBackground(java.awt.Color.lightGray);
			remoteStatus.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
			remoteStatus.setEditable(false);
		}
		return remoteStatus;
	}
	/**
	 * This method initializes historyComboBox
	 * 
	 * @return javax.swing.JComboBox
	 */
	private javax.swing.JComboBox getHistoryComboBox() {
		if (historyComboBox == null) {
			historyComboBox = new javax.swing.JComboBox();
			historyComboBox.setFont(
				new java.awt.Font("Dialog", java.awt.Font.BOLD, 10));
			historyComboBox.insertItemAt(new String("Pulldown to view history ..."),0);
			historyComboBox.setSelectedIndex(0);
			historyComboBox.addActionListener(this);
		}
		return historyComboBox;
	}
	/**
	 * This method initializes jProgressBar
	 * 
	 * @return javax.swing.JProgressBar
	 */
	private javax.swing.JProgressBar getJProgressBar() {
		if (jProgressBar == null) {
			jProgressBar = new javax.swing.JProgressBar();
		}
		return jProgressBar;
	}
	/**
	 * This method initializes connectionStatus
	 * 
	 * @return javax.swing.JTextField
	 */
	private javax.swing.JTextField getConnectionStatus() {
		if (connectionStatus == null) {
			connectionStatus = new javax.swing.JTextField();
			connectionStatus.setText("Connected...");
			connectionStatus.setBackground(java.awt.Color.lightGray);
			connectionStatus.setFont(
				new java.awt.Font("Dialog", java.awt.Font.PLAIN, 10));
		}
			connectionStatus.setEditable(false);
		return connectionStatus;
	}

	/**
	 * Implements Action listener.
	 */
	public void actionPerformed(ActionEvent evt) {
		System.out.println(evt.getSource());

		if (evt.getSource() == closeButton)
		{ // Close Button
			doClose();
		}
		else if (evt.getSource() == sendButton)
		{
			doSend();
		}
		else if (evt.getSource() == receiveButton)
		{
			doReceive();
		}
		else if (evt.getSource() == localDrivesComboBox)
		{
			changeLocalDrive();
		}
		else if (evt.getSource() == remoteDrivesComboBox)
		{ 
			changeRemoteDrive();
			remoteList.clear();
			remoteFileTable.setListData(remoteList);
		}
		else if (evt.getSource() == localTopButton)
		{
			changeLocalDrive();
		}
		else if (evt.getSource() == remoteTopButton)
		{
		  	changeRemoteDrive();
		}
		else if(evt.getSource() == deleteButton)
		{
			doDelete();
		}
		else if(evt.getSource()==newFolderButton)
		{
			doNewFolder();
		}
		else if (evt.getSource() == stopButton)
		{
			doStop();
		}

	}

	private void doNewFolder()
	{
		String name = JOptionPane.showInputDialog(null,"Enter new directory name", "Create New Directory", JOptionPane.QUESTION_MESSAGE);
		if(selectedTable.equals("remote"))
		{
			name = remoteLocation.getText()+name;
			viewer.rfb.createRemoteDirectory(name);
		}
		else
		{
			name = localLocation.getText()+name;
			File f = new File(name);
			f.mkdir();
			refreshLocalLocation();
			historyComboBox.insertItemAt(new String("Created Local Directory: " + name),0);
			historyComboBox.setSelectedIndex(0);
		}
	}
	private void doClose()
	{
		try {
			this.setVisible(false);
			viewer.rfb.writeFramebufferUpdateRequest(
									0,
									0,
									viewer.rfb.framebufferWidth,
									viewer.rfb.framebufferHeight,
									true);
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private void doDelete()
	{
		System.out.println("Delete Button Pressed");
		//Call this method to delete a file at server
		if(selectedTable.equals("remote"))
		{	
			String sFileName = ((String) this.remoteFileTable.getSelectedValue());
			
//			 sf@2004 - Directory can't be deleted
			if (sFileName.substring(0, 2).equals(" [") && sFileName.substring((sFileName.length() - 1), sFileName.length()).equals("]"))
			{
				JOptionPane.showMessageDialog(null, (String)"Directory Deletion is not yet available in this version...", "FileTransfer Info", JOptionPane.INFORMATION_MESSAGE);
				return;
			}			
			
			// for (int i = 0; i < remoteList.contains(size(); i++) 
			// 	remoteFileTable.g(i));			
			// sf@2004 - Delete prompt
			if (remoteList.contains(sFileName))
			{
				int r = JOptionPane.showConfirmDialog(null, "Are you sure you want to delete the file \n< " + sFileName + " >\n on Remote Machine ?", "File Transfer Warning", JOptionPane.YES_NO_OPTION);
				if (r == JOptionPane.NO_OPTION)
					return;
			}
			
			String fileName = remoteLocation.getText()+ sFileName.substring(1);
			viewer.rfb.deleteRemoteFile(fileName);
		}
		else
		{
			String sFileName = ((String) this.localFileTable.getSelectedValue());
			
//			 sf@2004 - Directory can't be deleted
			if (sFileName.substring(0, 2).equals(" [") && sFileName.substring((sFileName.length() - 1), sFileName.length()).equals("]"))
			{
				JOptionPane.showMessageDialog(null, (String)"Directory Deletion is not yet available in this version...", "FileTransfer Info", JOptionPane.INFORMATION_MESSAGE);
				return;
			}			
			// sf@2004 - Delete prompt
			if (localList.contains(sFileName))
			{
				int r = JOptionPane.showConfirmDialog(null, "Are you sure you want to delete the file \n< " + sFileName + " >\n on Local Machine ?", "File Transfer Warning", JOptionPane.YES_NO_OPTION);
				if (r == JOptionPane.NO_OPTION)
					return;
			}			
			String s = localLocation.getText() + sFileName.substring(1);
			File f = new File(s);
			f.delete();
			refreshLocalLocation();
			historyComboBox.insertItemAt(new String("Deleted On Local Disk: " + s),0);
			historyComboBox.setSelectedIndex(0);
		}
	}

	private void doReceive()
	{
		System.out.println("Received Button Pressed");

		String sFileName = ((String) this.remoteFileTable.getSelectedValue());
		
		// sf@2004 - Directory can't be transfered
		if (sFileName.substring(0, 2).equals(" [") && sFileName.substring((sFileName.length() - 1), sFileName.length()).equals("]"))
		{
			JOptionPane.showMessageDialog(null, (String)"Directory Transfer is not yet available in this version...", "FileTransfer Info", JOptionPane.INFORMATION_MESSAGE);
			return;
		}
		
		// sf@2004 - Overwrite prompt
		if (localList.contains(sFileName))
		{
			int r = JOptionPane.showConfirmDialog(null, "The file < " + sFileName + " >\n already exists on Local Machine\n Are you sure you want to overwrite it ?", "File Transfer Warning", JOptionPane.YES_NO_OPTION);
			if (r == JOptionPane.NO_OPTION)
				return;
		}
		
		//updateHistory("Downloaded " + localSelection.toString());
		String remoteFileName = this.remoteLocation.getText();
		remoteFileName+= ((String) this.remoteFileTable.getSelectedValue()).substring(1);
		
		String localDestinationPath = this.localLocation.getText()+((String)this.remoteFileTable.getSelectedValue()).substring(1);
		viewer.rfb.requestRemoteFile(remoteFileName,localDestinationPath);
	}

	private void doSend()
	{
		System.out.println("Send Button Pressed");

		String sFileName = ((String) this.localFileTable.getSelectedValue());
		
		// sf@2004 - Directory can't be transfered
		if (sFileName.substring(0, 2).equals(" [") && sFileName.substring((sFileName.length() - 1), sFileName.length()).equals("]"))
		{
			JOptionPane.showMessageDialog(null, (String)"Directory Transfer is not yet available in this version...", "FileTransfer Info", JOptionPane.INFORMATION_MESSAGE);
			return;
		}
		
		// sf@2004 - Overwrite prompt
		if (remoteList.contains(sFileName))
		{
			int r = JOptionPane.showConfirmDialog(null, "The file < " + sFileName + " >\n already exists on Remote Machine\n Are you sure you want to overwrite it ?", "File Transfer Warning", JOptionPane.YES_NO_OPTION);
			if (r == JOptionPane.NO_OPTION)
				return;
		}
		//updateHistory("Uploaded " + localSelection.toString());
		String source = this.localLocation.getText();
		source += ((String) this.localFileTable.getSelectedValue()).substring(1);
		
		String destinationPath = this.remoteLocation.getText();
		
		viewer.rfb.offerLocalFile(source,destinationPath); 
	}
	
	//
	// sf@2004 - The user stops the current file transfer
	// 
	private void doStop()
	{
		viewer.rfb.fAbort = true;
	}
	/**
	 * Update History: This method updates the history pulldown menu with the message string
	 *
	 */
	private void updateHistory(String message)
	{
		System.out.println("History: " + message);
		historyComboBox.insertItemAt(new String(message), 0);
	}
	
	/**
	 * This method updates the file table to the current selection of the remoteComboBox
	 *
	 */
	public void changeRemoteDrive()
	{
		remoteSelection = null;
	
		if (!updateDriveList) {
			String drive =	remoteDrivesComboBox.getSelectedItem().toString().substring(0,1)+ ":\\";
			viewer.rfb.readServerDirectory(drive);
			remoteLocation.setText(drive);
		}
		remoteList.clear();
		remoteFileTable.setListData(remoteList);
	}
	/**
	 * changeLocalDrive updates the file table
	 * to the current selection of the localComboBox
	 */
	private void changeLocalDrive()
	{
		File currentDrive = new File(localDrivesComboBox.getSelectedItem().toString());
		if(currentDrive.canRead())
		{
			localSelection = null;
			localStatus.setText("");
			changeLocalDirectory(currentDrive);
		}
		else
		{
			localList.clear();
			localStatus.setText("WARNING: Drive " + localDrivesComboBox.getSelectedItem().toString());
			connectionStatus.setText(" > WARNING - Local Drive unavailable (possibly restricted access or media not present)");
		}
	}
	/**
	 * Determines which FileTable was double-clicked and updates the table
	 */
	public void mouseClicked(MouseEvent e)
	{
		
		if(e.getClickCount() == 1)
		{								// Single clicked
			if (e.getSource() == localFileTable )
			{  			// on local file table 
				updateLocalFileTableSelection();
			}
			else if (e.getSource() == remoteFileTable)
			{
				updateRemoteFileTableSelection();						// on a remote file table
			}
		}
		else if (e.getClickCount() == 2)
		{						// Mouse Double clicked
			if (e.getSource() == localFileTable)
			{				// Clicked on local file
				updateLocalFileTable();
			}
			else if (e.getSource() == remoteFileTable)
			{		// Clicked on remote file
				updateRemoteFileTable();
			}
		}
	}
	/*
	 * Updates the globally accessible remote file selection if a file is single clicked in the RemoteFileTable
	 *
	 */
	private void updateRemoteFileTableSelection() {
		selectedTable = "remote";
		localFileTable.setBackground(new Color(238, 238, 238));
		remoteFileTable.setBackground(new Color(255, 255, 255));
		String name = (remoteFileTable.getSelectedValue().toString()).substring(1);
		if( !name.substring(0, 2).equals(" ["))	
			remoteSelection = remoteLocation.getText() + name.substring(0, name.length());
		
	}

	/*
	 * Updates the globally accessible local file selection 
	 * if a file is single clicked in the LocalFileTable 
	 *
	 */
	private void updateLocalFileTableSelection() {
		selectedTable="local";
		remoteFileTable.setBackground(new Color(238, 238, 238));
		localFileTable.setBackground(new Color(255, 255, 255));
		File currentSelection = new File(currentLocalDirectory, getTrimmedSelection());
		
		if(currentSelection.isFile()) 
			localSelection = currentSelection.getAbsoluteFile();

	}
	/**
	 * Updates the Remote File Table based on selection.  Called from mouseClicked handler
	 */
	public void updateRemoteFileTable() {
		String name = null;
		String action = null;
		String drive = null;
		name = (remoteFileTable.getSelectedValue().toString()).substring(1);

		if (name.equals("[..]"))
		{
			action = "up";
			remoteSelection = null;
			drive = remoteLocation.getText().substring(0, remoteLocation.getText().length() - 1);
			// JOptionPane.showMessageDialog(null, (String)drive, "FileTransfer DEBUG", JOptionPane.INFORMATION_MESSAGE);
			int index = drive.lastIndexOf("\\");
			drive = drive.substring(0, index + 1);

			remoteLocation.setText(drive);
			viewer.rfb.readServerDirectory(drive);
			remoteList.clear();
			remoteFileTable.setListData(remoteList);	
		}
		else if (!name.substring(0, 2).equals(" [") && !name.substring((name.length() - 1), name.length()).equals("]"))
		{
			action = "file";
			// Set the global remoteSelection field (used for get/put buttons)
			remoteSelection = remoteLocation.getText() + name.substring(0, name.length());
			drive = remoteLocation.getText();
			// ??
		}
		else
		{ 
			action = "down";
			remoteSelection = null;
			name = name.substring(1, name.length() - 1);
			drive = remoteLocation.getText() + name + "\\";
			remoteLocation.setText(drive);
			viewer.rfb.readServerDirectory(drive);
			remoteList.clear();
			remoteFileTable.setListData(remoteList);	
		}	
		//remoteLocation.setText(drive);	
	}
	/**
	 * Updates the Local File Table based on selection. Called from MouseClicked handler
	 */

	private void updateLocalFileTable()
	{
		localStatus.setText("");
		File currentSelection = new File(currentLocalDirectory , getTrimmedSelection());		// Selection

		if (getTrimmedSelection().equals(".."))
		{ // The [..] selected
			localSelection = null;	// No selection since directory changed
			currentSelection = currentLocalDirectory.getParentFile();
			if(currentSelection != null)
			{
				changeLocalDirectory(currentSelection);
			}
			else
			{
				localStatus.setText("You are at the root !"); 
			}
		}
		else if (currentSelection.isFile())
		{
			localSelection = currentSelection.getAbsoluteFile();
		}
		else if (currentSelection.isDirectory())
		{
			localSelection = null;	// No selection since directory changed
			changeLocalDirectory(currentSelection);
		}
	}

	/*
	 * Trims off the [] of a directory entry if it exists, else ignores it
	 * 
	 */
	private String getTrimmedSelection(){
		String currentSelection = (localFileTable.getSelectedValue().toString()).substring(1);
				if(currentSelection.substring(0,1).equals("[") &&
				currentSelection.substring(currentSelection.length()-1,currentSelection.length()).equals("]")){
				return currentSelection.substring(1,currentSelection.length()-1);
				} else {
					return currentSelection;
				}
	}

	/*
	 *  Reads the localDriveComboBox and returns the first readable drive for populating
	 *  the file table on load, so it's not looking at the A:\ drive when it opens. 
	 */
	 public File getFirstReadableLocalDrive(){
		File currentDrive;
		// sf@ - Select C: as default first readable drive
		for(int i = 0; i < localDrivesComboBox.getItemCount() ; i++)
		{
			currentDrive = new File(localDrivesComboBox.getItemAt(i).toString());
			if(localDrivesComboBox.getItemAt(i).toString().substring(0,1).toUpperCase().equals("C") && currentDrive.canRead())
			{
				localDrivesComboBox.setSelectedIndex(i);
				return currentDrive;
			}
		}
		// if C: not available, take the first readable drive, this time.
		for(int i = 0; i < localDrivesComboBox.getItemCount() ; i++)
		{
			currentDrive = new File(localDrivesComboBox.getItemAt(i).toString());
			if(currentDrive.canRead())
			{
				localDrivesComboBox.setSelectedIndex(i);
				return currentDrive;
			}
		}
		
		localStatus.setText("ERROR!: No Local Drives are Readable"); 
	 	return null;
	}
	

	/*
	 * Navigates the local file structure up or down one directory
	 */
	public void changeLocalDirectory(File dir)
	{
			currentLocalDirectory = dir;	// Updates Global
			File allFiles[] = dir.listFiles();	// Reads files
			String[] contents = dir.list();

			localList.clear();
			localList.addElement(" [..]");
			
			// Populate the Lists
			for (int i = 0; i < contents.length; i++)
			{
				if (allFiles[i].isDirectory())
					// localList.addElement("[" + contents[i] + "]");
					DirsList.add(" [" + contents[i] + "]"); // sf@2004
				else
				{
					// localList.addElement(contents[i]);
					FilesList.add(" " + contents[i]); // sf@2004
				}
			}
			// sf@2004
			for (int i = 0; i < DirsList.size(); i++) 
				localList.addElement(DirsList.get(i));
			for (int i = 0; i < FilesList.size(); i++) 
				localList.addElement(FilesList.get(i));
			
			FilesList.clear();
			DirsList.clear();
			
			localFileTable.setListData(localList);
			if(dir.toString().charAt(dir.toString().length()-1)==(File.separatorChar))
			{
				localLocation.setText(dir.toString());
			}
			else
			{
				localLocation.setText(dir.toString()+File.separator);	// Display updated location above file table
			}
			localStatus.setText("Total Files / Folders: " + (localList.size()-1));
	}
	public void mouseEntered(MouseEvent e) {
	}
	public void mouseExited(MouseEvent e) {
	}
	public void mousePressed(MouseEvent e) {
	}
	public void mouseReleased(MouseEvent e) {
	}

} //  @jve:visual-info  decl-index=0 visual-constraint="10,10"
